<template>
  <app-layout title="ホーム">
    <v-row no-gutters class="mt-5 mb-n5">
      <v-col cols="12" md="4" class="mb-4 mb-md-0 mt-md-3">
        <span class="mr-5">ジャンル：{{ genreName }}</span>
        <span>クイズ数：{{ quizCount }}</span>
      </v-col>

      <!-- ソートのセレクトボックス -->
      <v-col cols="12" md="3" offset-md="2">
        <sort-item
          :action-path="actionPath"
          :sort-items="sortItems"
          :sort-item="sortItem"
          :attached-url-params="['genre', 'q']"
        ></sort-item>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12" md="9" order="2" order-md="1">
        <v-alert
          v-if="!quizes.length"
          border="left"
          colored-border
          color="primary"
          elevation="2"
        >
          <div class="ml-3">クイズは存在しません。</div>
        </v-alert>

        <quiz-info
          v-for="quiz in quizes"
          :key="quiz.id"
          :quiz="quiz"
        ></quiz-info>
      </v-col>

      <!-- ブレークポイントmd以上で表示 -->
      <v-col order="2">
        <portal
          v-resize="onResize"
          to="genreListPlace"
          :disabled="DispGenreListPC"
          style="position: sticky; top: 100px;"
        >
          <genre-nav :genres="genres" :genre-list-id="genreListId"></genre-nav>
        </portal>
      </v-col>

      <!-- ブレークポイントmd未満で表示 -->
      <v-col order="1" class="d-block d-md-none">
        <v-menu transition="slide-x-transition" offset-y>
          <template #activator="{ on, attrs }">
            <v-btn color="primary " v-bind="attrs" v-on="on">
              ジャンル別
            </v-btn>
          </template>
          <portal-target name="genreListPlace"></portal-target>
        </v-menu>
      </v-col>
    </v-row>

    <!-- ページネーションを表示 -->
    <pagination
      v-if="quizes.length"
      :quiz-count="quizCount"
      :current-page="currentPage"
      :per-page="perPage"
      :action-path="actionPath"
      :attached-url-params="['genre', 'q', 'sort']"
    ></pagination>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import QuizInfo from '@/Components/QuizInfo'
import GenreNav from '@/Components/GenreNav'
import SortItem from '@/Components/SortItem'
import Pagination from '@/Components/Pagination'

import { SORT_ITEMS } from '@/util'

export default {
  components: {
    AppLayout,
    QuizInfo,
    GenreNav,
    SortItem,
    Pagination,
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
      type: Number,
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
    },
  },
  data() {
    return {
      DispGenreListPC: false,
      actionPath: 'home',
      sortItems: SORT_ITEMS,
    }
  },
  computed: {
    genreName() {
      return this.genres[this.genreListId].name
    },
  },
  methods: {
    onResize() {
      if (window.innerWidth < this.$vuetify.breakpoint.thresholds.sm) {
        this.DispGenreListPC = false
      } else {
        this.DispGenreListPC = true
      }
    },
  },
}
</script>
