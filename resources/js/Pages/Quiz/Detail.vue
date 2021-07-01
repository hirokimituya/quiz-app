<template>
  <app-layout>
    <v-card class="pa-3 px-md-16 my-5">
      <quiz-info
				:quiz="quiz"
        :detail="true"
			></quiz-info>

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

      <delete-quiz-form
        v-if="deleteButtonDisp"
        :quiz_id="quiz.id"
      ></delete-quiz-form>

      <!-- コメント入力 -->
      <div class="mb-4 ml-5">{{ comments.length }} 件のコメント</div>
      
      <v-row no-gutters class="mb-4">
        <v-col cols="2" sm="1" class="text-center">
          <v-avatar :size="45">
            <img :src="commentAuthorAvator" :alt="commentAuthorName">
          </v-avatar>
        </v-col>

        <v-col>
          <v-form @submit.prevent="commentSend">
            <v-textarea
              placeholder="コメント入力..."
              v-model="form.comment"
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
                  @click.prevent="form.comment = ''; commentBtnFlg = false; form.errors.comment = null;"
                >
                  キャンセル
                </v-btn>

                <v-btn
                  color="accent"
                  :disabled="!form.comment"
                  type="submit"
                >
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
        :comment="comment"
      ></comment>

    </v-card>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import QuizInfo from '@/Components/QuizInfo'
import Comment from '@/Components/Comment'
import DeleteQuizForm from './DeleteQuizForm'

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
    }
  },
  data() {
    return {
      form: this.$inertia.form({
        comment: '',
      }),
      commentBtnFlg: false,
    }
  },
  computed: {
    commentAuthorAvator() {
      return this.$page.props.user ? this.$page.props.user.profile_photo_url : '/storage/images/no_avator.jpg'
    },
    commentAuthorName() {
      return this.$page.props.user ? this.$page.props.user.name : '未ログイン'
    },
    deleteButtonDisp() {
      if (this.$page.props.user == null) {
        return false
      }
      else {
        return this.$page.props.user.id == this.quiz.user.id
      }
    }
  },
  methods: {
    startQuiz() {
      this.$inertia.get(route('quiz.answer', {
        quiz: this.quiz.id,
      }))
    },
    commentSend() {
      this.form.post(route('quiz.comment', {
        quiz: this.quiz.id,
      }), {
				onSuccess: () => {
          this.form.reset('comment')
          this.commentBtnFlg = false
        },
        preserveScroll: true,
			})
    },
    commentFoucus() {
      if (this.$page.props.user === null) {
        this.$inertia.get(route('login'));
      }

      this.commentBtnFlg = true
    },
  },
}
</script>