<template>
  <app-layout title="マイページ">
    <v-row>
      <v-col class="mt-md-5">
        <v-card style="position: sticky; top: 100px;">
          <v-card-text>
            <v-avatar class="mr-3" size="60">
              <img
                :src="dashboardUser.profile_photo_url"
                :alt="dashboardUser.name"
              />
            </v-avatar>
            {{ dashboardUser.name }}
            <v-btn
              v-if="createBtnShow"
              class="mt-4"
              block
              color="primary"
              @click.prevent="onCreate"
            >
              クイズ作成
            </v-btn>
            <v-btn
              v-if="gradeBtnShow"
              class="mt-4 white--text"
              block
              color="green"
              @click.prevent="onGradeList"
            >
              クイズ実行履歴
            </v-btn>
            <table class="mt-4 mx-auto mx-sm-0">
              <tr>
                <th class="text-left pa-2">クイズ作成数：</th>
                <td class="text-center pa-2">{{ quizCreateCount }}</td>
              </tr>
              <tr>
                <th class="text-left pa-2">総いいね数：</th>
                <td class="text-center pa-2">{{ likesTotalCount }}</td>
              </tr>
              <tr>
                <th class="text-left pa-2">総クイズ回答数:</th>
                <td class="text-center pa-2">{{ quizGradesCount }}</td>
              </tr>
            </table>

            <v-divider class="my-4"></v-divider>

            <v-list>
              <v-list-item-group
                v-model="selectedItem"
                color="primary"
                mandatory
              >
                <v-list-item v-for="item in items" :key="item.name">
                  <v-list-item-content>
                    <v-list-item-title>
                      {{ item.name }}
                    </v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list-item-group>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="9" order="2" order-md="1">
        <v-row>
          <v-col cols="12" sm="5" offset-sm="7">
            <!-- ソートのセレクトボックス -->
            <sort-item
              class="mt-2 mt-md-5 mb-2 mb-md-0"
              :action-path="actionPath"
              :action-param="{ user: dashboardUser.id }"
              :sort-items="sortItems"
              :sort-item="sortItem"
              :attached-url-params="['item']"
            ></sort-item>
          </v-col>
        </v-row>

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
    </v-row>

    <!-- ページネーションを表示 -->
    <pagination
      v-if="quizes.length"
      :quiz-count="quizCount"
      :current-page="currentPage"
      :per-page="perPage"
      :action-path="actionPath"
      :attached-url-params="['sort', 'item']"
    ></pagination>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import QuizInfo from '@/Components/QuizInfo'
import SortItem from '@/Components/SortItem'
import Pagination from '@/Components/Pagination'

import { getUrlParam, SORT_ITEMS } from '@/util'

export default {
  components: {
    AppLayout,
    QuizInfo,
    SortItem,
    Pagination,
  },
  props: {
    quizes: {
      type: Array,
      required: true,
    },
    dashboardUser: {
      type: Object,
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
    items: {
      type: Array,
      required: true,
    },
    itemListId: {
      type: Number,
      required: true,
    },
    sortItem: {
      type: String,
      required: true,
    },
    quizCreateCount: {
      type: Number,
      required: true,
    },
    likesTotalCount: {
      type: Number,
      required: true,
    },
    quizGradesCount: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      actionPath: 'dashboard',
      selectedItem: this.itemListId,
      sortItems: SORT_ITEMS,
    }
  },
  computed: {
    createBtnShow() {
      if (this.$page.props.user !== null) {
        return this.$page.props.user.id == this.dashboardUser.id
      }
      return false
    },
    gradeBtnShow() {
      if (this.dashboardUser.show_grade) {
        return true
      } else {
        if (this.$page.props.user !== null) {
          return this.$page.props.user.id == this.dashboardUser.id
        }
      }

      return false
    },
  },
  watch: {
    selectedItem() {
      let data = {}
      data.item = this.items[this.selectedItem].value

      let sort = getUrlParam('sort')
      if (sort !== '') {
        data.sort = sort
      }

      this.$inertia.get(
        route(this.actionPath, {
          user: this.dashboardUser.id,
        }),
        data,
      )
    },
  },
  methods: {
    onCreate() {
      this.$inertia.get(route('quiz.create'))
    },
    onGradeList() {
      this.$inertia.get(
        route('grade', {
          user: this.dashboardUser.id,
        }),
      )
    },
  },
}
</script>
