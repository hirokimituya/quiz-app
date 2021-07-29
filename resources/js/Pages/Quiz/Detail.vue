<template>
  <app-layout title="クイズ詳細">
    <v-card class="pa-3 px-md-16 my-5">
      <quiz-info :quiz="quiz" :detail="true"></quiz-info>

      <v-btn
        block
        type="button"
        color="primary"
        class="mb-12 text-subtitle-1"
        height="50"
        @click.prevent="startQuiz"
      >
        クイズを回答する
      </v-btn>

      <div v-if="editDeleteButtonDisp" class="text-right mt-n8">
        <!-- クイズ編集ボタン -->
        <v-btn
          color="green"
          class="white--text my-3"
          height="40"
          @click.stop="editQuiz"
        >
          クイズを編集する
        </v-btn>

        <!-- クイズ削除ボタン -->
        <delete-quiz-form :quiz-id="quiz.id"></delete-quiz-form>
      </div>

      <!-- クイズの成績表 -->
      <v-card
        v-if="userGradesAry.length >= 2"
        class="mx-auto text-center mt-5 mb-10"
        color="blue"
        max-width="800"
      >
        <v-card-text>
          <v-sheet color="rgba(0, 0, 0, .5)">
            <v-sparkline
              :value="userGradesAry"
              :gradient="gradient"
              gradient-direction="top"
              line-width="1"
              height="100"
              padding="15"
              stroke-linecap="round"
              smooth
              auto-draw
            >
              <template #label="item"> {{ item.value }}問 </template>
            </v-sparkline>
          </v-sheet>
        </v-card-text>

        <v-card-text>
          <div class="text-h5 font-weight-thin white--text">
            <v-row no-gutters justify="center">
              <v-col cols="12" md="3" class="mr-md-n10">
                クイズ正答数
              </v-col>
              <v-col md="4" class="ml-md-n6">
                （出題数 {{ itemsCount }}問）
              </v-col>
            </v-row>
          </div>
        </v-card-text>
      </v-card>

      <!-- コメント入力 -->
      <div class="mb-4 ml-5">{{ comments.length }} 件のコメント</div>

      <v-row no-gutters class="mb-4">
        <v-col cols="2" sm="1" class="text-center">
          <v-avatar :size="45">
            <img :src="commentAuthorAvator" :alt="commentAuthorName" />
          </v-avatar>
        </v-col>

        <v-col>
          <v-form @submit.prevent="commentSend">
            <v-textarea
              v-model="form.comment"
              placeholder="コメント入力..."
              counter="255"
              rows="1"
              @focus.prevent="commentFoucus"
            ></v-textarea>

            <!-- バリデーションエラー表示 -->
            <div v-if="form.errors.comment" class="ml-n10 ml-md-0">
              <v-alert
                color="error"
                border="left"
                dense
                class="white--text ml-n5 ml-md-0"
                elevation="2"
              >
                {{ form.errors.comment }}
              </v-alert>
            </div>

            <div v-show="commentBtnFlg" class="mt-2">
              <div class="text-right">
                <v-btn
                  color="secondary"
                  :disabled="form.processing"
                  @click.prevent="
                    form.comment = ''
                    commentBtnFlg = false
                    form.errors.comment = null
                  "
                >
                  キャンセル
                </v-btn>

                <v-btn color="accent" :disabled="!form.comment" type="submit">
                  コメント投稿
                </v-btn>
              </div>
            </div>
          </v-form>
        </v-col>
      </v-row>

      <comment
        v-for="(comment, key) in comments"
        :key="key"
        :quiz-id="quiz.id"
        :comment="comment"
      ></comment>
    </v-card>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import QuizInfo from '@/Components/QuizInfo'
import Comment from '@/Components/Comment'
import DeleteQuizForm from '@/Components/DeleteQuizForm'

const gradients = [
  ['#222'],
  ['#42b3f4'],
  ['red', 'orange', 'yellow'],
  ['purple', 'violet'],
  ['#00c6ff', '#F0F', '#FF0'],
  ['#f72047', '#ffd200', '#1feaea'],
]

export default {
  components: {
    AppLayout,
    QuizInfo,
    Comment,
    DeleteQuizForm,
  },
  props: {
    quiz: {
      type: Object,
      required: true,
    },
    comments: {
      type: Array,
      required: true,
    },
    userGradesAry: {
      type: Array,
      required: true,
    },
    itemsCount: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      form: this.$inertia.form({
        comment: '',
      }),
      commentBtnFlg: false,
      gradient: gradients[5],
      gradients,
    }
  },
  computed: {
    commentAuthorAvator() {
      var s3_origin = this.quiz.url.match(/(^https?:\/{2,}.*?)(?:\/|\?|#|$)/)[1]
      return this.$page.props.user
        ? this.$page.props.user.profile_photo_url
        : s3_origin + '/images/no_avator.jpg'
    },
    commentAuthorName() {
      return this.$page.props.user ? this.$page.props.user.name : '未ログイン'
    },
    editDeleteButtonDisp() {
      if (this.$page.props.user == null) {
        return false
      } else {
        return this.$page.props.user.id == this.quiz.user.id
      }
    },
  },
  methods: {
    startQuiz() {
      this.$inertia.get(
        route('quiz.answer', {
          quiz: this.quiz.id,
        }),
      )
    },
    editQuiz() {
      this.$inertia.get(
        route('quiz.edit', {
          quiz: this.quiz.id,
        }),
      )
    },
    commentSend() {
      this.form.post(
        route('quiz.comment', {
          quiz: this.quiz.id,
        }),
        {
          onSuccess: () => {
            this.form.reset('comment')
            this.commentBtnFlg = false
          },
          preserveScroll: true,
        },
      )
    },
    commentFoucus() {
      if (this.$page.props.user === null) {
        this.$inertia.get(
          route('login'),
          {},
          {
            headers: {
              'X-Login-Referer': true,
            },
          },
        )
      }

      this.commentBtnFlg = true
    },
  },
}
</script>
