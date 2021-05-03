<template>
  <app-layout>
    <v-card class="pa-3 px-md-16 my-5">
      <v-card-title class="text-h4 d-block text-center secondary--text">クイズ作成</v-card-title>

      <v-form @submit.prevent="onSubmit">
        <table class="mx-auto mt-3" width="100%">
          <tr>
            <th width="80px"><div class="mt-n6">タイトル</div></th>
            <td>
              <v-text-field
                outlined
                type="text"
                dense
                required
			  		    autofocus
                v-model="form.title"
              ></v-text-field>
            </td>
          </tr>
          <!-- バリデーションエラー表示 -->
          <tr v-if="form.errors.title">
            <th></th>
            <td>
              <v-alert 
                color="error"
                border="left"
                dense
                class="mt-n6 white--text"
                elevation="2"
              >
                {{ form.errors.title }}
              </v-alert>
            </td>
          </tr>

          <tr>
            <th width="80px"><div class="mt-n6">説明</div></th>
            <td>
              <v-textarea
                outlined
                type="text"
                dense
                v-model="form.description"
              ></v-textarea>
            </td>
          </tr>
          <!-- バリデーションエラー表示 -->
          <tr v-if="form.errors.description">
            <th></th>
            <td>
              <v-alert 
                color="error"
                border="left"
                dense
                class="mt-n6 white--text"
                elevation="2"
              >
                {{ form.errors.description }}
              </v-alert>
            </td>
          </tr>

          <tr>
            <th width="80px"><div class="mt-n6">ジャンル</div></th>
            <td>
              <v-select
                :items="genres"
		            v-model="form.genre"
		            outlined
		            dense
                item-text="name"
                item-value="id"
              >
              </v-select>
            </td>
          </tr>
          <!-- バリデーションエラー表示 -->
          <tr v-if="form.errors.genre">
            <th></th>
            <td>
              <v-alert 
                color="error"
                border="left"
                dense
                class="mt-n6 white--text"
                elevation="2"
              >
                {{ form.errors.genre }}
              </v-alert>
            </td>
          </tr>

          <tr>
            <th width="80px"><div class="mt-n6">画像</div></th>
            <td>
              <v-file-input
                outlined
                dense
                v-model="form.image"
              ></v-file-input>
            </td>
          </tr>
          <!-- バリデーションエラー表示 -->
          <tr v-if="form.errors.image">
            <th></th>
            <td>
              <v-alert 
                color="error"
                border="left"
                dense
                class="mt-n6 white--text"
                elevation="2"
              >
                {{ form.errors.image }}
              </v-alert>
            </td>
          </tr>
        </table>

        <v-row class="my-2">
          <v-col cols="7" md="3">
            <v-select
              :items="questionCount(10)"
              filled
              dense
              v-model="questionNum"
              label="問題数"
            ></v-select>
          </v-col>
        </v-row>

        <quiz-item
          v-for="num in questionNum"
          :key="num"
          v-model="form.question[num]"
          :errors="questionErrors(num)"
          :num="num"
        ></quiz-item>

        <v-row no-gutters>
          <v-col md="6" offset-md="3">
            <v-btn
              block
              type="submit"
              color="primary"
              class="mt-8 mb-12"
              :disabled="form.processing"
            >
              送信確認
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

export default {
  components: { 
    AppLayout,
    QuizItem,
  },
  props: {
    genres: {
      type: Array,
      required: true,
    }
  },
  data() {
    return {
      form: this.$inertia.form({
        title: '',
        description: '',
        genre: '',
        image: [],
        question: {1:{},2:{},3:{},4:{},5:{},6:{},7:{},8:{},9:{},10:{}},
      }),
      questionNum: 1,
    }
  },
  methods: {
    questionCount(i) {
      return [...Array(i)].map((_, i) => i + 1)
    },
    onSubmit() {
      this.form
        .transform(data => ({
          ...data,
          question: data.question.slice(0, this.questionNum),
        }))
        .post(route('quiz.create.conf'), {
          forceFormData: true,
        })
    },
    questionErrors(num) {
      let ret;
      try {
        ret = this.form.errors.question[num]
      } catch (err) {
        ret = {}
      }
      return ret;
    }
  }
}
</script>