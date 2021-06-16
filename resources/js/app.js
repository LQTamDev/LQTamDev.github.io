require('./bootstrap');

// Import modules...
import { createApp, h } from 'vue';
import { App as InertiaApp, plugin as InertiaPlugin } from '@inertiajs/inertia-vue3';
import axios from 'axios'
import VueHtmlToPaper from './Plugins/VueHtmlToPaper';
import excel from 'vue-excel-export';
import JsonCSV from 'vue-json-csv'

const el = document.getElementById('app');

const app = createApp({
  data() {
    return {
      roleArray: [],
      axios: axios
    };
  },
  mounted() {
    if (!window.location.pathname.includes('login')  && !window.location.pathname.includes('register')){
      this.fetchRoles();
    }
  },
  methods: {
    async fetchRoles() {
      let { data } = await axios.get(this.route("roles.show"))
    this.roleArray = data
    },
  },
  computed:{
    roleArrayComputed(){
      return this.roleArray
    }
  },
  render: () =>
  h(InertiaApp, {
    initialPage: JSON.parse(el.dataset.page),
    resolveComponent: (name) => require(`./Pages/${name}`).default,
    resolveErrors: page => (page.props.errors || {}),
  }),
})
.mixin({ methods: { route } })
  .use(InertiaPlugin)
  .use(VueHtmlToPaper)
  app.use(excel)
  // app.component('downloadCsv',JsonCSV)
app.mount(el);