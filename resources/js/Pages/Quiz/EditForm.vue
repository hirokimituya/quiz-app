<template>
  <app-layout title="クイズ編集">
    <div class="text-center mt-7 text-h4">
      クイズ編集
    </div>
    <v-card class="pa-3 px-md-16 my-5 pt-5">
      <v-form @submit.prevent="onSubmit">
        <table class="mx-auto mt-3" width="100%">
          <tr>
            <th width="80px"><div class="mt-n6">タイトル</div></th>
            <td>
              <v-text-field
                v-model="form.title"
                outlined
                type="text"
                dense
                required
                autofocus
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
                v-model="form.description"
                outlined
                type="text"
                dense
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
              <v-row>
                <v-col md="6">
                  <v-select
                    v-model="form.genre"
                    :items="genres"
                    outlined
                    dense
                    item-text="name"
                    item-value="id"
                  >
                  </v-select>
                </v-col>
              </v-row>
            </td>
          </tr>
          <!-- バリデーションエラー表示 -->
          <tr v-if="form.errors.genre">
            <th></th>
            <td>
              <v-row>
                <v-col md="6">
                  <v-alert
                    color="error"
                    border="left"
                    dense
                    class="mt-n6 white--text"
                    elevation="2"
                  >
                    {{ form.errors.genre }}
                  </v-alert>
                </v-col>
              </v-row>
            </td>
          </tr>

          <tr>
            <th width="80px"><div class="mt-n6">画像</div></th>
            <td>
              <v-file-input v-model="form.image" outlined dense></v-file-input>
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
          <tr>
            <th></th>
            <td>
              <div v-if="!!currentImage">
                <div v-if="!form.imageDeleteFlg">
                  <img :src="currentImage" alt="クイズ用画像" width="200px" />
                  <v-btn
                    color="red"
                    class="white--text"
                    @click.prevent="form.imageDeleteFlg = true"
                  >
                    画像削除
                  </v-btn>
                </div>
                <div v-else>
                  <v-btn
                    color="grey"
                    class="white--text"
                    @click.prevent="form.imageDeleteFlg = false"
                  >
                    画像を戻す
                  </v-btn>
                </div>
              </div>
            </td>
          </tr>
        </table>

        <v-row class="my-2">
          <v-col cols="7" md="3">
            <v-select
              v-model="questionNum"
              :items="questionCount(10)"
              filled
              dense
              label="問題数"
              type="number"
            ></v-select>
          </v-col>
        </v-row>

        <draggable id="draggable" :animation="200" @end="onEnd">
          <quiz-item-form
            v-for="num in questionNum"
            :id="num"
            :key="key[num - 1]"
            v-model="form.question[num2eng(num)]"
            :num="num"
            @delete="onDelete"
          ></quiz-item-form>
        </draggable>

        <v-row no-gutters>
          <v-col md="6" offset-md="3">
            <v-btn
              block
              type="submit"
              color="primary"
              class="mt-8"
              :disabled="form.processing"
            >
              送信確認
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
import QuizItemForm from '@/Components/QuizItemForm'

import draggable from 'vuedraggable'

import { num2eng, eng2num, quizItemDelete } from '@/util'

export default {
  components: {
    AppLayout,
    QuizItemForm,
    draggable,
  },
  props: {
    genres: {
      type: Array,
      required: true,
    },
    quiz: {
      type: Object,
      required: false,
      default: null,
    },
    items: {
      type: Object,
      required: false,
      default: function() {
        return {}
      },
    },
  },
  remember: ['form', 'questionNum', 'currentImage'],
  data() {
    return {
      form: this.$inertia.form({
        title: '',
        description: '',
        genre: '',
        image: [],
        question: {
          one: {},
          two: {},
          three: {},
          four: {},
          five: {},
          six: {},
          seven: {},
          eight: {},
          nine: {},
          ten: {},
        },
        imageDeleteFlg: false,
      }),
      questionNum: 1,
      key: [],
      currentImage: null,
    }
  },
  watch: {
    questionNum: {
      handler() {
        for (let i = 1; i <= this.questionNum; i++) {
          if (!Object.keys(this.form.question[num2eng(i)]).length) {
            this.form.question[num2eng(i)] = {
              question: null,
              answerFormat: 1,
              answerText: null,
              answerRadio: 1,
              answerCheck: [1],
              selectItemsNum: 2,
              selectItemText: { one: '', two: '', three: '', four: '' },
            }
          }
        }
      },
      immediate: true,
    },
  },
  created() {
    if (this.quiz !== null && !this.$browserBackFlg) {
      this.form.title = this.quiz.title ?? ''
      this.form.description = this.quiz.description ?? ''
      this.form.genre = this.quiz.genre.id ?? ''
      this.form.question = { ...this.form.question, ...this.items }
      this.questionNum = Object.keys(this.items).length
      if (this.quiz.filename !== null) {
        this.currentImage = this.quiz.url
      }
    }
  },
  mounted() {
    this.createKey()
  },
  methods: {
    questionCount(i) {
      return [...Array(i)].map((_, i) => i + 1)
    },
    onSubmit() {
      const questionData = {}
      Object.keys(this.form.question).forEach(key => {
        if (eng2num(key) <= this.questionNum) {
          questionData[key] = this.form.question[key]

          switch (questionData[key].selectItemsNum) {
            case 2:
              delete questionData[key].selectItemText['three']
              delete questionData[key].selectItemText['four']
              break
            case 3:
              delete questionData[key].selectItemText['four']
              break
          }
        }
      })

      this.form
        .transform(data => ({
          ...data,
          question: questionData,
        }))
        .post(
          route('quiz.edit.conf', {
            quiz: this.quiz.id,
          }),
          {
            forceFormData: true,
          },
        )
    },
    num2eng(num) {
      return num2eng(num)
    },
    createKey() {
      let S = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
      let array_count = Object.keys(this.form.question).length
      this.key = [...Array(array_count)].map(() => {
        return Array.from(Array(12))
          .map(() => S[Math.floor(Math.random() * S.length)])
          .join('')
      })
    },
    onEnd() {
      let children = document.getElementById('draggable').childNodes
      let new_question = {}
      for (let key in Object.keys(children)) {
        let question_new_num = Number(key) + 1
        let question_old_num = Number(children[key].id)
        new_question[num2eng(question_new_num)] = this.form.question[
          num2eng(question_old_num)
        ]
      }
      new_question = Object.assign({}, this.form.question, new_question)
      this.form.question = new_question
      // QuizItemFormを強制更新するためにkeyプロパティを変更する
      this.createKey()
    },
    onDelete(item_num) {
      if (this.questionNum <= 1) {
        return alert('問題が1つの場合は、削除できません。')
      }

      this.form.question = quizItemDelete(this.form.question, item_num)

      this.questionNum--

      // QuizItemFormを強制更新するためにkeyプロパティを変更する
      this.createKey()
    },
    back() {
      history.back()
    },
  },
}
</script>
