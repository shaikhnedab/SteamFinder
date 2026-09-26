/* ============================================================
   SteamFinder — local vanilla JS
   Zero dependencies.
   ============================================================ */
(function () {
    'use strict';

    /* Translations injected by the Blade layout. */
    var I18N = window.STEAMFINDER_I18N || {};
    function t(key, fallback) {
        return I18N[key] || fallback;
    }

    /* ---------- Theme toggle ---------- */

    var html = document.documentElement;
    var themeToggle = document.getElementById('themeToggle');
    var STORAGE_KEY = 'steamfinder-theme';

    function getPreferredTheme() {
        var saved = null;
        try {
            saved = localStorage.getItem(STORAGE_KEY);
        } catch (e) {
            /* storage unavailable — fall through to the OS preference */
        }
        if (saved) return saved;
        return window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
    }

    function applyTheme(theme) {
        html.setAttribute('data-theme', theme);
        try {
            localStorage.setItem(STORAGE_KEY, theme);
        } catch (e) {
            /* storage unavailable — the theme still applies for this page */
        }
    }

    /* The inline <head> script already resolved and applied the theme to
       avoid a flash of the wrong canvas; re-assert it here so behaviour
       stays correct if that script is ever removed. */
    applyTheme(getPreferredTheme());

    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            var current = html.getAttribute('data-theme') || 'dark';
            applyTheme(current === 'dark' ? 'light' : 'dark');
        });
    }

    /* ---------- Toast ---------- */

    var toastHost = null;

    function toast(message) {
        if (!toastHost) {
            toastHost = document.createElement('div');
            toastHost.className = 'toast-host';
            toastHost.setAttribute('role', 'status');
            toastHost.setAttribute('aria-live', 'polite');
            document.body.appendChild(toastHost);
        }

        var el = document.createElement('div');
        el.className = 'toast';
        el.textContent = message;
        toastHost.appendChild(el);

        requestAnimationFrame(function () {
            el.classList.add('show');
        });

        window.setTimeout(function () {
            el.classList.remove('show');
            window.setTimeout(function () {
                if (el.parentNode) el.parentNode.removeChild(el);
            }, 250);
        }, 2200);
    }

    /* ---------- Flash message dismiss ---------- */

    document.querySelectorAll('[data-dismiss-flash]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var flash = btn.closest('.flash');
            if (!flash) return;
            flash.classList.add('is-hidden');
            window.setTimeout(function () {
                if (flash.parentNode) flash.parentNode.removeChild(flash);
            }, 260);
        });
    });

    /* ---------- Copy to clipboard ---------- */

    function copyText(text, btn) {
        var fallback = function () {
            var ok = false;
            try {
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.setAttribute('readonly', '');
                ta.style.position = 'fixed';
                ta.style.top = '0';
                ta.style.left = '-9999px';
                document.body.appendChild(ta);
                ta.select();
                ta.setSelectionRange(0, text.length);
                ok = document.execCommand('copy');
                document.body.removeChild(ta);
            } catch (e) {
                ok = false;
            }
            if (ok) showCopied(btn);
            else toast(t('copyFailed', 'Copy failed'));
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(
                function () { showCopied(btn); },
                fallback
            );
        } else {
            fallback();
        }
    }

    function showCopied(btn) {
        var row = btn.closest('[data-copy-row]');
        if (row) row.classList.add('is-copied');
        btn.classList.add('is-copied');
        btn.setAttribute('aria-label', t('copied', 'Copied'));

        toast(t('copied', 'Copied to clipboard'));

        window.clearTimeout(btn._copyTimer);
        btn._copyTimer = window.setTimeout(function () {
            btn.classList.remove('is-copied');
            if (row) row.classList.remove('is-copied');
            var label = btn.getAttribute('data-copy-label') || 'Copy';
            btn.setAttribute('aria-label', label);
        }, 1600);
    }

    function valueFor(selector) {
        var el = document.querySelector(selector);
        return el ? el.textContent.trim() : '';
    }

    document.addEventListener('click', function (e) {
        var target = e.target;

        /* clicking a copy button */
        var btn = target.closest ? target.closest('[data-copy]') : null;
        if (btn) {
            var text = valueFor(btn.getAttribute('data-copy'));
            if (text) copyText(text, btn);
            return;
        }

        /* clicking anywhere else on a copy row also copies */
        var row = target.closest ? target.closest('[data-copy-row]') : null;
        if (row && !target.closest('a') && !target.closest('button')) {
            var rowBtn = row.querySelector('[data-copy]');
            if (rowBtn) {
                var rowText = valueFor(rowBtn.getAttribute('data-copy'));
                if (rowText) copyText(rowText, rowBtn);
            }
        }
    });
})();
