<template>
  <v-app>
    <v-app-bar app color="primary" elevate-on-scroll>
      <v-toolbar-title class="white--text text-h5 text-sm-h4">
        <button @click.prevent="onHome()">
          QuizMaker
        </button>
      </v-toolbar-title>

      <!-- ブレークポイントmd以上なら検索フォームをヘッダーに表示 -->
      <portal
        v-resize="onResize"
        class="mt-6 ml-7"
        to="searchPlace"
        :disabled="DispSearchPC"
      >
        <v-form>
          <v-row no-gutters>
            <v-col cols="10" md="11">
              <v-text-field
                v-model="search"
                class="inline rounded-0"
                label="キーワードを入力"
                single-line
                dense
                solo
                clearable
                type="search"
              ></v-text-field>
            </v-col>
            <v-col cols="1">
              <v-btn
                type="submit"
                class="orange rounded-0"
                height="38px"
                @click.prevent="onSearch()"
              >
                <v-icon>{{ mdiMagnify }}</v-icon>
              </v-btn>
            </v-col>
          </v-row>
        </v-form>
      </portal>

      <v-spacer></v-spacer>

      <div v-if="user" class="mr-lg-16">
        <v-menu offset-y left origin="top right" transition="scale-transition">
          <template #activator="{ on, attrs }">
            <v-btn icon class="mr-md-16" v-bind="attrs" v-on="on">
              <v-avatar>
                <img
                  :src="$page.props.user.profile_photo_url"
                  :alt="$page.props.user.name"
                />
              </v-avatar>
            </v-btn>
          </template>
          <v-list min-width="150">
            <v-list-item @click="onDashboard">
              <v-list-item-content>
                <v-list-item-subtitle>
                  マイページ
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-list-item @click="onProfile">
              <v-list-item-content>
                <v-list-item-subtitle>
                  プロフィール
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-list-item @click="onLogout">
              <v-list-item-content>
                <v-list-item-subtitle>
                  ログアウト
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-menu>
      </div>

      <div v-else>
        <v-btn
          class="white--text px-1 px-md-6 py-7 py-md-8 rounded-0"
          color="primary darken-2"
          elevation="0"
          @click.prevent="onRegister()"
        >
          ユーザ登録
        </v-btn>
        <v-btn
          class="white--text px-1 px-md-6 py-7 py-md-8 rounded-0"
          color="primary"
          elevation="0"
          @click.prevent="onLogin()"
        >
          ログイン
        </v-btn>
      </div>
    </v-app-bar>

    <v-main>
      <v-container style="max-width: 1100px;" class="mx-auto">
        <!-- ブレークポイントmd未満なら検索フォームをヘッダーの下に表示 -->
        <portal-target
          class="mx-2 mt-2 mb-n4"
          name="searchPlace"
        ></portal-target>

        <slot></slot>

        <!-- 上に戻るボタン -->
        <v-fab-transition>
          <v-btn
            v-show="scrollBtnFlg"
            v-scroll="onScroll"
            fab
            large
            fixed
            right
            bottom
            color="primary"
            @click="$vuetify.goTo(0)"
          >
            <v-icon>{{ mdiChevronUp }}</v-icon>
          </v-btn>
        </v-fab-transition>
      </v-container>
    </v-main>

    <v-footer padless color="secondary">
      <v-col class="text-center white--text" cols="12">
        {{ new Date().getFullYear() }} — <strong>QuizMaker</strong>
      </v-col>
    </v-footer>
  </v-app>
</template>

<script>
import { mdiMagnify, mdiChevronUp } from '@mdi/js'
import { getUrlParam } from '@/util'

export default {
  data() {
    return {
      mdiMagnify,
      mdiChevronUp,
      search: '',
      DispSearchPC: false,
      scrollBtnFlg: false,
      user: this.$page.props.user,
    }
  },
  watch: {
    search(newVal) {
      if (!newVal) {
        let data = {}
        for (let param of ['genre', 'sort']) {
          let item = getUrlParam(param)
          if (item !== '') {
            data[param] = item
          }
        }

        this.$inertia.get(route('home'), data)
      }
    },
  },
  mounted() {
    this.search = getUrlParam('q')
  },
  methods: {
    onHome() {
      this.$inertia.get(route('home'))
    },
    onRegister() {
      this.$inertia.get(route('register'))
    },
    onLogin() {
      this.$inertia.get(route('login'))
    },
    onSearch() {
      let data = {}
      data.q = this.search

      for (let param of ['genre', 'sort']) {
        let item = getUrlParam(param)
        if (item !== '') {
          data[param] = item
        }
      }

      this.$inertia.get(route('home'), data)
    },
    onDashboard() {
      this.$inertia.get(
        route('dashboard', {
          user: this.$page.props.user.id,
        }),
      )
    },
    onProfile() {
      this.$inertia.get(route('profile.show'))
    },
    onLogout() {
      this.user = false
      this.$inertia.post(route('logout'))
    },
    onScroll() {
      if (window.scrollY > 500) {
        this.scrollBtnFlg = true
      } else {
        this.scrollBtnFlg = false
      }
    },
    onResize() {
      if (window.innerWidth < this.$vuetify.breakpoint.thresholds.sm) {
        this.DispSearchPC = false
      } else {
        this.DispSearchPC = true
      }
    },
  },
}
</script>
