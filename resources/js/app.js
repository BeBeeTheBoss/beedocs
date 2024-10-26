import './bootstrap';
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
//css
import '../css/app.css';

// Import the CSS or use your own!
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

//bootstrap
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.min.js';

//font-awesome
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faCircleQuestion } from '@fortawesome/free-regular-svg-icons';
import { faPlus,faAngleDown,faCircleCheck,faEllipsisVertical,faTrashCan,faPenToSquare } from '@fortawesome/free-solid-svg-icons';
library.add(faPlus,faAngleDown,faCircleCheck,faCircleQuestion,faEllipsisVertical,faTrashCan,faPenToSquare)

// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const vuetify = createVuetify({
    components,
    directives,
  })

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(Toast)
      .use(vuetify)
      .component('font-awesome-icon', FontAwesomeIcon)
      .mount(el)
  },
})
