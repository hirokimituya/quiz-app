<template>
  <app-layout title="クイズ回答結果">
    <div class="text-center mt-7 text-h4">
      クイズ回答結果
    </div>
    <v-card class="pa-3 px-md-16 my-5">
      <quiz-info :quiz="quiz" :detail="true"></quiz-info>

      <v-row class="mt-n6 mb-3">
        <v-col offset-md="2">
          <div class="red--text text-h4">
            {{ questionNum }} 問中 {{ correctCount }} 問 正解
          </div>
        </v-col>
      </v-row>

      <quiz-item-answer
        v-for="num in questionNum"
        :key="num"
        :item="answers[num2eng(num)]"
        :num="num"
      ></quiz-item-answer>

      <v-row no-gutters>
        <v-col md="6" offset-md="3">
          <v-btn
            block
            type="button"
            color="secondary"
            class="mt-8 mb-12"
            @click.prevent="onMovePage"
          >
            {{ backBtnString }}
          </v-btn>
        </v-col>
      </v-row>
    </v-card>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import QuizInfo from '@/Components/QuizInfo'
import QuizItemAnswer from '@/Components/QuizItemAnswer'

import { num2eng } from '@/util'

export default {
  components: {
    AppLayout,
    QuizInfo,
    QuizItemAnswer,
  },
  props: {
    quiz: {
      type: Object,
      required: true,
    },
    answers: {
      type: Object,
      required: true,
    },
    correctCount: {
      type: Number,
      required: true,
    },
    referrerPage: {
      type: String,
      default: 'AnswerConfirm',
    },
  },
  data() {
    return {
      refferrerAnswerPage: this.referrerPage == 'AnswerConfirm',
      backBtnString:
        this.referrerPage == 'AnswerConfirm'
          ? 'クイズ詳細ページに戻る'
          : 'クイズ実行履歴ページに戻る',
    }
  },
  computed: {
    questionNum() {
      return Object.keys(this.answers).length
    },
  },
  methods: {
    onMovePage() {
      if (this.refferrerAnswerPage) {
        this.$inertia.get(
          route('quiz.detail', {
            quiz: this.quiz.id,
          }),
        )
      } else {
        history.back()
      }
    },
    num2eng(num) {
      return num2eng(num)
    },
  },
}
</script>
