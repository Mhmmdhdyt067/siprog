import './bootstrap';
import $ from 'jquery';
import Alpine from 'alpinejs';
import AOS from 'aos';
import 'aos/dist/aos.css';
import hljs from 'highlight.js';
import 'highlight.js/styles/atom-one-dark.css';
import Chart from 'chart.js/auto';

window.$ = window.jQuery = $;
window.Chart = Chart;
window.Alpine = Alpine;

Chart.defaults.font.family = "'Plus Jakarta Sans', 'Figtree', system-ui, sans-serif";
Chart.defaults.font.size = 11;
Chart.defaults.color = '#64748B';
Chart.defaults.borderColor = 'rgba(11, 11, 12, 0.07)';
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.boxWidth = 8;
Chart.defaults.plugins.legend.labels.boxHeight = 8;
Chart.defaults.plugins.legend.labels.padding = 14;
Chart.defaults.plugins.tooltip.padding = 10;
Chart.defaults.plugins.tooltip.cornerRadius = 10;
Chart.defaults.plugins.tooltip.titleFont = { weight: '700' };

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

Alpine.store('toasts', {
    items: [],
    seq: 0,
    push(message, type = 'success') {
        if (!message) return;
        const id = ++this.seq;
        this.items.push({ id, message, type });
        setTimeout(() => this.remove(id), 5200);
    },
    remove(id) {
        this.items = this.items.filter((toast) => toast.id !== id);
    },
});

Alpine.store('confirm', {
    open: false,
    busy: false,
    title: '',
    message: '',
    confirmText: 'Ya, lanjutkan',
    cancelText: 'Batal',
    tone: 'danger',
    onConfirm: null,
    show(options = {}) {
        this.title = options.title ?? 'Konfirmasi tindakan';
        this.message = options.message ?? '';
        this.confirmText = options.confirmText ?? 'Ya, lanjutkan';
        this.cancelText = options.cancelText ?? 'Batal';
        this.tone = options.tone ?? 'danger';
        this.onConfirm = typeof options.onConfirm === 'function' ? options.onConfirm : null;
        this.busy = false;
        this.open = true;
    },
    close() {
        if (this.busy) return;
        this.open = false;
    },
    async run() {
        if (this.busy) return;
        this.busy = true;
        try {
            if (this.onConfirm) await this.onConfirm();
            this.open = false;
        } catch (error) {
            // The caller already reports its own failure to the user.
        } finally {
            this.busy = false;
        }
    },
});

const Sipro = {
    toast(message, type = 'success') {
        Alpine.store('toasts').push(message, type);
    },

    openModal(name) {
        window.dispatchEvent(new CustomEvent('open-modal', { detail: name }));
    },

    closeModal(name) {
        window.dispatchEvent(new CustomEvent('close-modal', { detail: name }));
    },

    confirm(options) {
        Alpine.store('confirm').show(options);
    },

    applyHtml(html) {
        if (!html) return;
        Object.entries(html).forEach(([selector, markup]) => {
            document.querySelectorAll(selector).forEach((element) => {
                element.innerHTML = markup;
            });
        });

        document.dispatchEvent(new CustomEvent('sipro:html-applied'));
    },

    applyFields(fields) {
        if (!fields) return;
        Object.entries(fields).forEach(([key, value]) => {
            document.querySelectorAll(`[data-field="${key}"]`).forEach((el) => {
                el.textContent = value;
            });
            document.querySelectorAll(`[data-profile-field="${key}"]`).forEach((el) => {
                el.textContent = value;
            });
            document.querySelectorAll(`[data-user-${key}]`).forEach((el) => {
                el.textContent = value;
            });
        });
    },

    clearErrors($form) {
        $form.find('[data-error]').text('').addClass('hidden');
        $form.find('.is-invalid').removeClass('is-invalid border-rose-400 ring-2 ring-rose-200');
    },

    showErrors($form, errors) {
        this.clearErrors($form);
        const entries = Object.entries(errors ?? {});
        if (!entries.length) return;

        entries.forEach(([key, messages]) => {
            const $error = $form.find(`[data-error="${key}"]`).first();
            $error.text(Array.isArray(messages) ? messages[0] : messages).removeClass('hidden');
            $error.closest('[data-field-group]')
                .find('input, select, textarea')
                .first()
                .addClass('is-invalid border-rose-400 ring-2 ring-rose-200');
        });

        const $first = $form.find('[data-error]').not('.hidden').first();
        $first.closest('[data-field-group]').find('input, select, textarea').first().trigger('focus');

        this.toast('Periksa kembali isian yang ditandai.', 'error');
    },

    submit($form, options = {}) {
        const form = $form[0];
        if (!form || $form.data('busy')) return;

        const $buttons = $form.find('[type="submit"]');
        const original = $buttons.first().html();
        const data = new FormData(form);

        if (!data.has('_method')) {
            const method = ($form.attr('method') || 'post').toUpperCase();
            if (method !== 'POST') data.append('_method', method);
        }

        $form.data('busy', true);
        this.clearErrors($form);
        $buttons.prop('disabled', true);
        $buttons.first().html('<span class="inline-flex items-center gap-2"><span class="h-3 w-3 animate-spin rounded-full border-2 border-current border-t-transparent"></span>Menyimpan…</span>');

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data,
            processData: false,
            contentType: false,
            dataType: 'json',
            headers: { Accept: 'application/json' },
        })
            .done((response) => {
                this.toast(response.message || 'Perubahan berhasil disimpan.', 'success');
                this.applyHtml(response.html);
                this.applyFields(response.fields);

                if (response.redirect) {
                    window.location.href = response.redirect;
                    return;
                }

                if (response.reload) {
                    window.location.reload();
                    return;
                }

                const $modal = $form.closest('[data-modal-name]');
                if ($modal.length && !$form.is('[data-stay-open]')) {
                    this.closeModal($modal.data('modal-name'));
                }

                if ($form.is('[data-reset-on-success]')) {
                    form.reset();
                }

                if (typeof options.onSuccess === 'function') {
                    options.onSuccess(response);
                }
            })
            .fail((xhr) => {
                const response = xhr.responseJSON;

                if (xhr.status === 422) {
                    this.showErrors($form, response?.errors);
                    return;
                }

                this.toast(response?.message || 'Terjadi kesalahan. Coba lagi.', 'error');
            })
            .always(() => {
                $form.removeData('busy');
                $buttons.prop('disabled', false);
                $buttons.first().html(original);
            });
    },

    destroy(url, options = {}) {
        return $.ajax({
            url,
            method: 'DELETE',
            dataType: 'json',
            headers: { Accept: 'application/json' },
        })
            .done((response) => {
                this.toast(response.message || 'Data berhasil dihapus.', 'success');
                this.applyHtml(response.html);
                this.applyFields(response.fields);

                if (response.redirect) {
                    window.location.href = response.redirect;
                }
            })
            .fail((xhr) => {
                this.toast(xhr.responseJSON?.message || 'Gagal menghapus data.', 'error');
            });
    },

    post(url, data = {}) {
        return $.ajax({
            url,
            method: 'POST',
            data,
            dataType: 'json',
            headers: { Accept: 'application/json' },
        });
    },

    fillForm($modal, values = {}) {
        const form = $modal.find('form')[0];
        if (form) form.reset();

        $modal.find('[data-error]').text('').addClass('hidden');

        Object.entries(values).forEach(([key, value]) => {
            if (key.startsWith('__')) {
                $modal.find(`[data-fill-text="${key.slice(2)}"]`).text(value ?? '');
                return;
            }

            const $fields = $modal.find(`[name="${key}"], [name="${key}[]"]`);
            if (!$fields.length) return;

            if ($fields.is(':checkbox')) {
                const selected = Array.isArray(value) ? value.map(String) : [String(value)];
                $fields.each(function () {
                    this.checked = selected.includes(String(this.value));
                });
            } else if ($fields.is(':radio')) {
                $fields.each(function () {
                    this.checked = String(this.value) === String(value);
                });
            } else if ($fields.is('select[multiple]')) {
                const selected = Array.isArray(value) ? value.map(String) : [];
                $fields.find('option').each(function () {
                    this.selected = selected.includes(String(this.value));
                });
            } else {
                $fields.val(value ?? '');
            }
        });
    },

    openFormModal(name, options = {}) {
        const $modal = $(`[data-modal-name="${name}"]`);
        if (!$modal.length) return;

        const $form = $modal.find('form[data-ajax]').first();

        if ($form.length) {
            if (options.action) $form.attr('action', options.action);

            if (options.method) {
                $form.find('input[name="_method"]').remove();
                if (options.method.toUpperCase() !== 'POST') {
                    $form.append(`<input type="hidden" name="_method" value="${options.method.toUpperCase()}">`);
                }
            }
        }

        this.fillForm($modal, options.fill ?? {});

        if (options.title) $modal.find('[data-modal-title]').text(options.title);
        if (options.submit) $modal.find('[data-modal-submit]').text(options.submit);
        if (options.description) $modal.find('[data-modal-description]').text(options.description);

        this.openModal(name);
    },

    chart(id, config) {
        const canvas = document.getElementById(id);
        if (!canvas) return null;

        if (canvas._chart) canvas._chart.destroy();
        canvas._chart = new Chart(canvas, config);

        return canvas._chart;
    },
};

window.Sipro = Sipro;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    AOS.init({
        duration: 800,
        easing: 'ease-out-cubic',
        offset: 70,
        once: false,
        mirror: true,
    });

    // Efek 3D: kartu hero mengikuti gerakan pointer (desktop saja)
    const hero = document.querySelector('[data-hero-3d]');
    const heroInner = hero?.querySelector('.hero-3d-inner');
    const finePointer = window.matchMedia('(pointer: fine)').matches;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (hero && heroInner && finePointer && !reduceMotion) {
        let frame = 0;

        hero.addEventListener(
            'pointermove',
            (event) => {
                if (frame) return;

                frame = window.requestAnimationFrame(() => {
                    frame = 0;
                    const rect = hero.getBoundingClientRect();
                    const px = Math.min(Math.max((event.clientX - rect.left) / rect.width, 0), 1) - 0.5;
                    const py = Math.min(Math.max((event.clientY - rect.top) / rect.height, 0), 1) - 0.5;

                    heroInner.style.setProperty('--hero-tilt-x', `${(-py * 4).toFixed(2)}deg`);
                    heroInner.style.setProperty('--hero-tilt-y', `${(px * 5).toFixed(2)}deg`);
                });
            },
            { passive: true },
        );

        hero.addEventListener('pointerleave', () => {
            heroInner.style.setProperty('--hero-tilt-x', '0deg');
            heroInner.style.setProperty('--hero-tilt-y', '0deg');
        });
    }

    window.addEventListener('load', () => AOS.refresh());

    document.querySelectorAll('pre code').forEach((el) => {
        hljs.highlightElement(el);
    });

    document.addEventListener('sipro:html-applied', () => {
        document.querySelectorAll('pre code:not([data-highlighted])').forEach((el) => {
            hljs.highlightElement(el);
        });
    });

    const flash = document.getElementById('server-flash');
    if (flash) {
        if (flash.dataset.success) Sipro.toast(flash.dataset.success, 'success');
        if (flash.dataset.error) Sipro.toast(flash.dataset.error, 'error');
    }
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': csrfToken(),
        'X-Requested-With': 'XMLHttpRequest',
    },
});

$(document).on('submit', 'form[data-ajax]', function (event) {
    event.preventDefault();
    Sipro.submit($(this));
});

$(document).on('click', '[data-form-modal]', function () {
    const button = this;
    const data = { ...button.dataset };
    let fill = {};

    if (data.fill) {
        try {
            fill = JSON.parse(data.fill);
        } catch (error) {
            fill = {};
        }
    }

    Sipro.openFormModal(data.formModal, {
        action: data.action,
        method: data.method,
        title: data.title,
        submit: data.submit,
        description: data.modalDescription,
        fill,
    });
});

$(document).on('click', '[data-delete]', function () {
    const button = this;
    const url = button.dataset.delete;

    Sipro.confirm({
        title: button.dataset.title || 'Hapus data ini?',
        message: button.dataset.message || 'Data yang dihapus tidak dapat dikembalikan.',
        confirmText: button.dataset.confirm || 'Ya, hapus',
        tone: 'danger',
        onConfirm: () => Sipro.destroy(url),
    });
});

$(document).on('click', '[data-logout]', function () {
    Sipro.confirm({
        title: 'Keluar dari akun?',
        message: 'Sesi Anda akan diakhiri dan Anda perlu masuk kembali untuk melanjutkan.',
        confirmText: 'Ya, keluar',
        tone: 'danger',
        onConfirm: () => {
            document.getElementById('logout-form')?.submit();
        },
    });
});

$(document).on('input change', 'form[data-ajax] input, form[data-ajax] select, form[data-ajax] textarea', function () {
    $(this).removeClass('is-invalid border-rose-400 ring-2 ring-rose-200');
    $(this).closest('[data-field-group]').find('[data-error]').addClass('hidden');
});

$(document).on('click', '[data-modal-close]', function () {
    const name = $(this).closest('[data-modal-name]').data('modal-name');
    if (name) Sipro.closeModal(name);
});

// Sinkronkan token CSRF setelah navigasi dari halaman login.
$(document).ajaxSend((event, jqXHR, settings) => {
    settings.headers = settings.headers || {};
    settings.headers['X-CSRF-TOKEN'] = csrfToken();
});
