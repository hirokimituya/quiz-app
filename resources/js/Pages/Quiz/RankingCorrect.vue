<template>
  <app-layout title="クイズ正解ランキング">
    <div class="text-center mt-7 text-h4">
      クイズ正解ランキング
    </div>
    <v-card class="pa-3 px-md-16 my-5">
      <v-card-title class="mb-5">
        <v-row no-gutters justify="end">
          <v-col cols="4" sm="2" class="mt-5 ml-n2 mt-sm-0">
            <v-select
              v-model="numItem"
              :items="numItems"
              label="表示数"
              outlined
              dense
            ></v-select>
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
            v-for="user in usersList"
            id="user-list"
            :key="user.grade_id"
            class="text-center"
            @click.stop="goGradePage(user.id)"
          >
            <td>{{ user.ranking }}位</td>
            <td>
              <a @click.stop.prevent="goUserPage(user.id)">
                {{ user.name }}
              </a>
            </td>
            <td>
              {{ user.total_correct_count }}
            </td>
          </tr>
        </tbody>
      </v-simple-table>

      <!-- ページネーションを表示 -->
      <pagination
        v-if="usersList.length"
        :quiz-count="userCount"
        :current-page="currentPage"
        :per-page="perPage"
        :action-path="actionPath"
        :attached-url-params="['disp_num']"
        class="mt-5"
      ></pagination>

      <v-row no-gutters>
        <v-col md="6" offset-md="3">
          <v-btn
            block
            type="submit"
            color="secondary"
            class="mt-8 mb-12"
            @click.prevent="goHomePage"
          >
            トップページに戻る
          </v-btn>
        </v-col>
      </v-row>
    </v-card>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Pagination from '@/Components/Pagination'

export default {
  components: {
    AppLayout,
    Pagination,
  },
  props: {
    usersList: {
      type: Array,
      required: true,
    },
    userCount: {
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
  },
  data() {
    return {
      table_headers: ['ランキング', 'ユーザ', '総正解数'],
      numItems: [15, 30, 50, 80, 100],
      actionPath: 'ranking.correct',
      numItem: this.perPage,
    }
  },
  watch: {
    numItem() {
      this.$inertia.get(route(this.actionPath), {
        disp_num: this.numItem,
      })
    },
  },
  methods: {
    goGradePage(user_id) {
      this.$inertia.get(
        route('grade', {
          user: user_id,
        }),
      )
    },
    goHomePage() {
      this.$inertia.get(route('home'))
    },
    goUserPage(user_id) {
      this.$inertia.get(route('dashboard', { user: user_id }))
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
tr#user-list {
  cursor: pointer;
}
</style>
