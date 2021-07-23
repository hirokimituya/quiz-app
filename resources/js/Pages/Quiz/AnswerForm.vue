<template>
  <app-layout>
    <v-card class="pa-3 px-md-16 my-5">
      <quiz-info :quiz="quiz" :detail="true"></quiz-info>

      <v-form @submit.prevent="onSubmit">
        <quiz-item
          v-for="num in questionNum"
          :key="num"
          v-model="form.question[num2eng(num)]"
          :num="num"
        ></quiz-item>

        <v-row no-gutters>
          <v-col md="6" offset-md="3">
            <v-btn
              block
              type="submit"
              color="primary"
              class="mt-8"
              :disabled="form.processing"
            >
              回答確認
            </v-btn>
          </v-col>
        </v-row>

        <v-row no-gutters>
          <v-col md="6" offset-md="3">
            <v-btn
              block
              type="submit"
              color="secondary"
              class="mt-8 mb-12"
              @click.prevent="back"
            >
              クイズ詳細ページに戻る
            </v-btn>
          </v-col>
        </v-row>
      </v-form>
    </v-card>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import QuizInfo from '@/Components/QuizInfo'
import QuizItem from '@/Components/QuizItem'

import { num2eng } from '@/util'

export default {
  components: {
    AppLayout,
    QuizInfo,
    QuizItem,
  },
  props: {
    quiz: {
      type: Object,
      required: true,
    },
    items: {
      type: Object,
      required: true,
    },
  },
  remember: ['form'],
  data() {
    return {
      form: this.$inertia.form({
        question: this.items,
      }),
    }
  },
  computed: {
    questionNum() {
      return Object.keys(this.form.question).length
    },
  },
  methods: {
    onSubmit() {
      this.form.post(
        route('quiz.answer.conf', {
          quiz: this.quiz.id,
        }),
      )
    },
    back() {
      history.back()
    },
    num2eng(num) {
      return num2eng(num)
    },
  },
  mounted() {
    this.$vuetify.goTo(0, { duration: 0 })
  },
}
</script>
