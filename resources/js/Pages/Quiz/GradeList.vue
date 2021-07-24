<template>
  <app-layout>
    <div class="text-center mt-7 text-h4">
      クイズ実行履歴
    </div>
    <v-card class="pa-3 px-md-16 my-5">
      <v-card-title class="mb-5">
        <v-row no-gutters justify="space-between">
          <v-col cols="12" sm="6">
            <a text-decoration="none" @click.stop.prevent="mypage">
              <v-avatar class="mr-3" size="60">
                <img :src="gradeUser.profile_photo_url" :alt="gradeUser.name" />
              </v-avatar>
            </a>
            <a
              class="black--text"
              text-decoration="none"
              @click.stop.prevent="mypage"
            >
              {{ gradeUser.name }}
            </a>
          </v-col>
          <v-col cols="4" sm="2" class="mt-5 ml-n2 mt-sm-0">
            <v-select
              v-model="numItem"
              :items="numItems"
              label="表示数"
              outlined
              dense
            ></v-select>
          </v-col>
          <v-col cols="8" sm="4" class="mt-5 mt-sm-0">
            <sort-item
              :action-path="actionPath"
              :action-param="actionPathParam"
              :sort-items="sortItems"
              :sort-item="sortItem"
              :attached-url-params="['disp_num']"
            ></sort-item>
          </v-col>
        </v-row>
      </v-card-title>
      <v-simple-table>
        <thead>
          <tr>
            <th
              v-for="header in table_headers"
              :key="header.key"
              class="text-md-h6 text-center"
            >
              {{ header }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="grade in gradesList"
            :key="grade.grade_id"
            class="text-center"
          >
            <td>
              <inertia-link
                :href="route('quiz.detail', { quiz: grade.quiz_id })"
              >
                {{ grade.quiz_title }}
              </inertia-link>
            </td>
            <td>
              <inertia-link
                :href="route('home')"
                :data="{ genre: grade.genre_id }"
              >
                {{ grade.genre_name }}
              </inertia-link>
            </td>
            <td>
              <inertia-link :href="route('dashboard', { user: grade.user_id })">
                {{ grade.user_name }}
              </inertia-link>
            </td>
            <td>
              {{ grade.items_count }}
            </td>
            <td>
              {{ grade.correct_count }}
            </td>
            <td>
              {{ grade.created_at }}
            </td>
          </tr>
        </tbody>
      </v-simple-table>

      <!-- ページネーションを表示 -->
      <pagination
        v-if="gradesList.length"
        :quiz-count="gradeCount"
        :current-page="currentPage"
        :per-page="perPage"
        :action-path="actionPath"
        :action-path-param="actionPathParam"
        :attached-url-params="['disp_num', 'sort']"
        class="mt-5"
      ></pagination>

      <v-row no-gutters>
        <v-col md="6" offset-md="3">
          <v-btn
            block
            type="submit"
            color="secondary"
            class="mt-8 mb-12"
            @click.prevent="back"
          >
            "{{ gradeUser.name }}"さんのマイページに戻る
          </v-btn>
        </v-col>
      </v-row>
    </v-card>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Pagination from '@/Components/Pagination'
import SortItem from '@/Components/SortItem'

import { getUrlParam } from '@/util'

export default {
  components: {
    AppLayout,
    Pagination,
    SortItem,
  },
  props: {
    gradesList: {
      type: Array,
      required: true,
    },
    gradeUser: {
      type: Object,
      required: true,
    },
    gradeCount: {
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
    sortItem: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      table_headers: [
        'クイズタイトル',
        'ジャンル名',
        '作成ユーザ',
        '出題数',
        '正解数',
        '回答日時',
      ],
      actionPath: 'grade',
      actionPathParam: { user: this.gradeUser.id },
      sortItems: [
        { value: 'quiz_title', text: 'クイズタイトル' },
        { value: 'latest', text: '回答日時' },
      ],
      numItems: [15, 30, 50, 80, 100],
      numItem: this.perPage,
    }
  },
  watch: {
    numItem() {
      let data = {}

      data.disp_num = this.numItem

      let item
      for (let param of ['sort']) {
        item = getUrlParam(param)
        if (item !== '') {
          data[param] = item
        }
      }

      this.$inertia.get(route(this.actionPath, this.actionPathParam), data)
    },
  },
  methods: {
    mypage() {
      this.$inertia.get(
        route('dashboard', {
          user: this.gradeUser.id,
        }),
      )
    },
    back() {
      history.back()
    },
  },
}
</script>

<style scoped>
td a {
  text-decoration: none;
  color: black;
}
td a:hover {
  text-decoration: underline;
  color: blue;
}
</style>
