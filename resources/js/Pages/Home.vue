<template>
	<app-layout>
		<v-row no-gutters class="mt-5 mb-n5">
			<v-col cols="6" md="3" class="mt-3">
				<div>ジャンル：{{ genreName }}</div>
			</v-col>

			<v-col cols="6" md="4" offset-md="2">
				<v-select
					:items="sortItems"
					v-model="selectSortItem"
					label="ソート"
					outlined
					dense
				></v-select>
			</v-col>
		</v-row>
		
		<v-row>

			<v-col cols="12" md="9" order="2" order-md="1">
				<quiz-info
					v-for="quiz in quizes"
					:quiz="quiz"
					:key="quiz.id"
				></quiz-info>
			</v-col>

			<!-- ブレークポイントmd以上で表示 -->
			<v-col order="2">
				<portal 
					to="genreListPlace" 
					:disabled="DispGenreListPC"
					v-resize="onResize"
					style="position: sticky; top: 100px;"
				>
					<genre-nav 
						:genres="genres"
						:genreListId="genreListId"
					></genre-nav>
				</portal>
			</v-col>

			<!-- ブレークポイントmd未満で表示 -->
			<v-col order="1" class="d-block d-md-none mb-n4">
				<v-menu
					transition="slide-x-transition"
					offset-y
				>
					<template #activator="{ on, attrs }">
						<v-btn
							color="primary "
							v-bind="attrs"
							v-on="on"
						>
							ジャンル別
						</v-btn>
					</template>
					<portal-target name="genreListPlace"></portal-target>
				</v-menu>
			</v-col>

		</v-row>

		<!-- ページネーションを表示 -->
		<v-pagination
			v-model="page"
      class="mb-8"
      :length="pageCount"
      :total-visible="10"
		></v-pagination>

	</app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import QuizInfo from '@/Components/QuizInfo'
import GenreNav from '@/Components/GenreNav'

import { getUrlParam } from '@/util'

export default {
	components: {
		AppLayout,
		QuizInfo,
		GenreNav,
	},
	props: {
		quizes: {
			type: Array,
			required: true,
		},
		quizCount: {
			type: Number,
			default: 0,
		},
		currentPage: {
			type: Number,
			default: 1,
		},
		perPage: {
			type:Number,
			required: true,
		},
		genres: {
			type: Array,
			required: true,
		},
		genreListId: {
			type: Number,
			default: 0,
		},
		sortItem: {
			type: String,
			required: true,
		}
	},
	data() {
		return {
			DispGenreListPC: false,
			sortItems: [
				{ value: 'quiz', text: 'クイズ回答回数順' },
				{ value: 'like', text: 'いいね数順' },
				{ value: 'comment', text: 'コメント数順'},
			],
			selectSortItem: this.sortItem,
			page: this.currentPage,
		}
	},
	computed: {
		genreName() {
			return this.genres[this.genreListId].name
		},
		pageCount() {
			return Math.ceil(this.quizCount / this.perPage)
		}
	},
	methods: {
		onResize() {
			if (window.innerWidth < this.$vuetify.breakpoint.thresholds.sm){
				this.DispGenreListPC = false
			}
			else {
				this.DispGenreListPC = true
			}
		}
	},
	watch: {
		selectSortItem() {
			let data = {}

			let genre = getUrlParam('genre')
			if (genre !== '') {
				data.genre = genre
			}

			data.sort = this.selectSortItem

			this.$inertia.get(route('home'), data);
		},
		page() {
			let data = {}

			let genre = getUrlParam('genre')
			if (genre !== '') {
				data.genre = genre
			}

			let sort = getUrlParam('sort')
      if (sort !== '') {
        data.sort = sort
      }

			data.page = this.page

			this.$inertia.get(route('home'), data);
		}
	}
}
</script>