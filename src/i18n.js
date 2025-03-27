import { createI18n } from 'vue-i18n';
import ja from './locales/ja.json';
import no from './locales/no.json';

export default createI18n({
  legacy: false,
  locale: localStorage.getItem('locale') || import.meta.env.VITE_I18N_LOCALE || 'ja',
  fallbackLocale: import.meta.env.VITE_I18N_FALLBACK_LOCALE || 'ja',
  messages: {
    no,
    ja
  }
})
