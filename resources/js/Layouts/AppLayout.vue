<template>
  <v-app>
    <v-app-bar 
      app 
      color="primary"  
      elevate-on-scroll
    >
      <v-toolbar-title class="white--text text-h5 text-sm-h4">
       <button @click.prevent="onHome()">
         QuizMaker
        </button>
      </v-toolbar-title>

			<!-- ブレークポイントmd以上なら検索フォームをヘッダーに表示 -->
			<portal 
				class="mt-6 ml-7"
				to="searchPlace"
				v-resize="onResize" 
				:disabled="DispSearchPC"
			>
				<v-form>
					<v-row no-gutters>
						<v-col cols="10" md="11">
        			<v-text-field
								class="inline rounded-0"
        			  label="キーワードを入力"
        			 	single-line
								dense
								solo
								type="search"
								v-model="search"
        			></v-text-field>
						</v-col>
						<v-col cols="1">
        			<v-btn
								type="submit"
								class="orange rounded-0"
								height='38px'
								@click.prevent="onSearch()"
							>
								<v-icon>{{ mdiMagnify }}</v-icon>
							</v-btn>
						</v-col>
					</v-row>
				</v-form>
			</portal>

     <v-spacer></v-spacer>
     
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
    </v-app-bar>

		<v-main>
			<v-container style="max-width: 1100px;" class="mx-auto">
				<!-- ブレークポイントmd未満なら検索フォームをヘッダーの下に表示 -->
				<portal-target 
					class="mx-2"
					name="searchPlace"
				></portal-target>

        <slot></slot>

				<!-- 上に戻るボタン -->
        <v-fab-transition>
          <v-btn
						v-scroll="onScroll"
            v-show="scrollBtnFlg"
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
  	  <v-col
  	    class="text-center white--text"
  	    cols="12"
  	  >
  	    {{ new Date().getFullYear() }} — <strong>QuizMaker</strong>
  	  </v-col>
  	</v-footer>
  </v-app>
</template>

<script>
import { mdiMagnify, mdiChevronUp } from '@mdi/js'

export default {
  data() {
    return {
      mdiMagnify, mdiChevronUp,
			search: '',
			DispSearchPC: false,
			scrollBtnFlg: false,
    }
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
			console.log(this.search)
		},
		onScroll() {
      if (window.scrollY > 500) {
        this.scrollBtnFlg = true
      }
      else {
        this.scrollBtnFlg = false
      }
    },
		onResize() {
			if (window.innerWidth < this.$vuetify.breakpoint.thresholds.sm){
				this.DispSearchPC = false
			}
			else {
				this.DispSearchPC = true
			}
		}
  }
}
</script>
