<template>
  <app-layout title="クイズ編集確認">
    <div class="text-center mt-7 text-h4">
      クイズ編集確認
    </div>
    <v-card class="pa-3 px-md-16 my-5 pt-5">
      <v-form @submit.prevent="onSubmit">
        <table class="mx-auto mb-3" width="90%">
          <tr>
            <th width="120px" class="text-left pb-4">タイトル</th>
            <td class="text-h6 pb-4">
              {{ form.title }}
            </td>
          </tr>

          <tr v-if="form.description">
            <th class="text-left pb-4">説明</th>
            <td class="text-h6 pb-4">
              {{ form.description }}
            </td>
          </tr>

          <tr>
            <th class="text-left pb-4">ジャンル</th>
            <td class="text-h6 pb-4">
              {{ getGenre }}
            </td>
          </tr>

          <tr v-if="form.image">
            <th class="text-left pb-4">画像</th>
            <td>
              <img :src="form.image" alt="クイズ用画像" width="200px" />
            </td>
          </tr>
        </table>

        <quiz-item
          v-for="num in questionNum"
          :key="num"
          v-model="form.question[num2eng(num)]"
          readonly
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
              クイズ編集
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
              クイズ編集画面に戻る
            </v-btn>
          </v-col>
        </v-row>
      </v-form>
    </v-card>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import QuizItem from '@/Components/QuizItem'

import { num2eng } from '@/util'

export default {
  components: {
    AppLayout,
    QuizItem,
  },
  props: {
    formData: {
      type: Object,
      required: true,
    },
    genres: {
      type: Array,
      required: true,
    },
    quizId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      form: this.$inertia.form({
        title: this.formData.title,
        description: this.formData.description,
        genre: this.formData.genre,
        image: this.formData.image,
        question: this.formData.question,
        imageDeleteFlg: this.formData.imageDeleteFlg,
      }),
    }
  },
  computed: {
    questionNum() {
      return Object.keys(this.form.question).length
    },
    getGenre() {
      let ret
      this.genres.forEach(genre => {
        if (genre.id == this.form.genre) {
          ret = genre.name
        }
      })

      return ret
    },
  },
  methods: {
    onSubmit() {
      this.form.patch(
        route('quiz.edit', {
          quiz: this.quizId,
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
}
</script>
