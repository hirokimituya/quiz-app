<template>
  <app-layout>
    <div class="text-center mt-7 text-h4">
      クイズ実行履歴
    </div>
    <v-card class="pa-3 px-md-16 my-5">
      <v-card-title class="mb-5">
        <v-row no-gutters justify="space-between">
          <v-col cols="12" sm="5">
            <a
              text-decoration="none"
              @click.stop.prevent="mypage"
            >
              <v-avatar class="mr-3" size="60">
				      	<img :src="grade_user.profile_photo_url" :alt="grade_user.name">
				      </v-avatar>
            </a>
            <a
              class="black--text"
              text-decoration="none"
              @click.stop.prevent="mypage"
            >
			      	{{ grade_user.name }}
            </a>
          </v-col>
          <v-col sm="4" class="mt-5 mt-sm-0">
            <sort-item
            	:actionPath="actionPath"
              :actionParam="actionPathParam"
            	:sortItems="sortItems"
            	:sortItem="sortItem"
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

      <!-- ページネーションを表示 -->
		  <pagination
		  	v-if="grades_list.length"
		  	:quizCount="gradeCount"
		  	:currentPage="currentPage"
		  	:perPage="perPage"
		  	:actionPath="actionPath"
        :actionPathParam="actionPathParam"
        class="mt-5"
		  ></pagination>
    </v-card>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Pagination from '@/Components/Pagination'
import SortItem from '@/Components/SortItem'

export default {
  components: {
    AppLayout,
    Pagination,
    SortItem,
  },
  props: {
    grades_list: {
      type: Array,
      required: true,
    },
    grade_user: {
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
			type:Number,
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
      actionPathParam: { user: this.grade_user.id },
      sortItems: [
        { value: 'quiz_title', text: 'クイズタイトル' },
        { value: 'latest', text: '回答日時' },
      ],
    }
  },
  methods: {
    mypage() {
      this.$inertia.get(route('dashboard', {
        user: this.grade_user.id,
      }))
    }
  }
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