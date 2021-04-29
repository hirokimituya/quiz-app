<template>
  <v-app>
    <v-app-bar 
      app 
      color="blue"  
      elevate-on-scroll
    >
      <v-toolbar-title class="white--text font-black">
       <button @click.prevent="onHome()">
         QuizMaker
        </button>
      </v-toolbar-title>
			
			<v-spacer class="d-block d-md-none"></v-spacer>

      <v-form 
				class="mt-6 ml-7 d-none d-md-block"
			>
				<v-row no-gutters>
					<v-col cols="11">
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
							class="orange rounded-0"
							height='38px'
							@click.prevent="onSearch()"
						>
							<v-icon>{{ mdiMagnify }}</v-icon>
						</v-btn>
					</v-col>
				</v-row>
      </v-form>

			<v-btn 
				icon 
				class="d-block d-md-none"
				@click="dispSearch = !dispSearch"
			>
				<v-icon>{{ mdiMagnify }}</v-icon>
			</v-btn>

     <v-spacer class="d-none d-md-block"></v-spacer>
     
     <v-btn 
       class="white--text px-1 px-md-6 py-7 py-md-8 rounded-0" 
       color="blue darken-3" 
       elevation="0" 
       @click.prevent="onRegister()"
     >
       ユーザ登録
     </v-btn>
     <v-btn 
       class="white--text px-1 px-md-6 py-7 py-md-8 rounded-0" 
       color="blue" 
       elevation="0" 
       @click.prevent="onLogin()"
     >
       ログイン
     </v-btn>
    </v-app-bar>

		<v-form 
				class="mt-16 mx-2 mb-n16"
				v-show="dispSearch"
		>
			<v-row no-gutters>
				<v-col cols="10">
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
						class="orange rounded-0"
						height='38px'
						@click.prevent="onSearch()"
					>
						<v-icon>{{ mdiMagnify }}</v-icon>
					</v-btn>
				</v-col>
			</v-row>
    </v-form>

    <v-main>
      <v-container>
        <slot></slot>
      </v-container>
    </v-main>

		<v-footer padless color="grey darken-1">
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
import {mdiMagnify} from '@mdi/js'

export default {
  data() {
    return {
      mdiMagnify,
			search: '',
			dispSearch: false,
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
		}
  }
}
</script>
