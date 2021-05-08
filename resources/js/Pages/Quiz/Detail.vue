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

      <!-- コメント入力 -->
      <div>2件のコメント</div>
      
      <v-form @submit.prevent="commentSend">
        <v-textarea
          placeholder="コメント入力..."
          v-model="comment"
          count="255"
          rows="1"
          @focus.prevent="commentBtnFlg = true"
        ></v-textarea>

        <div v-show="commentBtnFlg">
          <div class="text-right">
            <v-btn
              color="secondary"
              @click.prevent="comment = ''; commentBtnFlg = false;"
            >
              キャンセル
            </v-btn>

            <v-btn
              color="accent"
              :disabled="!comment"
              type="submit"
            >
              コメント投稿
            </v-btn>
          </div>
        </div>
      </v-form>

      <comment
      ></comment>

    </v-card>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import QuizInfo from '@/Components/QuizInfo'
import Comment from '@/Components/Comment'

export default {
  components: { 
    AppLayout,
    QuizInfo,
    Comment,
  },
  props: {
    quiz: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      comment: '',
      commentBtnFlg: false,
    }
  },
  methods: {
    startQuiz() {
      this.$inertia.get(route('quiz.answer', {
        quiz: this.quiz.id,
      }))
    },
    commentSend() {

    },
  },
}
</script>