import Vue from 'vue'
import Vuetify from 'vuetify/lib'

import colors from 'vuetify/lib/util/colors'

Vue.use(Vuetify)

const opts = {
  theme: {
    themes: {
      light: {
        primary: colors.blue.lighten1,
        secondary: colors.grey.darken1,
        accent: colors.cyan,
      }
    }
  },
  icons: {
    iconfont: 'mdiSvg',
  },
}

export default new Vuetify(opts)
