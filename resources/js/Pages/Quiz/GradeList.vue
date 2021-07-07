<template>
  <app-layout>
    <v-card class="pa-3 px-md-16 my-5">
      <v-card-title class="text-h4 mb-5">
        クイズ実行履歴
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
            v-for="grade in grades_list"
            :key="grade.grade_id"
            class="text-center"
          >
            <td>
              <inertia-link 
                :href="route('quiz.detail', {quiz: grade.quiz_id})" 
              >
                {{ grade.quiz_title }}
              </inertia-link>
            </td>
            <td>
              {{ grade.genre_name }}
            </td>
            <td>
              <inertia-link :href="route('dashboard', {user: grade.user_id})">
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
    </v-card>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'

export default {
  components: {
    AppLayout,
  },
  props: {
    grades_list: {
      type: Array,
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
    }
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
  color: blue
}
</style>