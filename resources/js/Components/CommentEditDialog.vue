<template>
  <v-dialog v-model="editDialog" max-width="500">
    <v-card>
      <v-card-title>
        コメント編集
      </v-card-title>
      <v-card-text>
        <div class="mb-3">
          コメントの編集内容を入力してください。
        </div>

        <v-textarea
          v-model="form.comment"
          placeholder="コメント入力..."
          counter="255"
          rows="1"
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
      </v-card-text>

      <v-card-actions class="mt-n5 grey lighten-4">
        <v-spacer></v-spacer>
        <v-btn class="my-1" @click="closeModal">
          キャンセル
        </v-btn>
        <v-btn
          color="green"
          class="white--text my-1"
          :disabled="commentEditDisabled"
          @click="editComment"
        >
          コメント編集
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: {
    value: {
      type: Boolean,
      required: true,
    },
    quizId: {
      type: Number,
      required: true,
    },
    comment: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      form: this.$inertia.form({
        comment: this.comment.content,
      }),
      editDialog: false,
      commentEditDisabled: false,
    }
  },
  watch: {
    value: function(val) {
      this.editDialog = val
    },
    editDialog: function(val) {
      this.$emit('input', val)
    },
  },
  methods: {
    editComment() {
      this.commentEditDisabled = true

      this.form.patch(
        route('quiz.comment.edit', {
          quiz: this.quizId,
          comment: this.comment.id,
        }),
        {
          preserveScroll: true,
          onSuccess: () => {
            this.form.reset('comment')
            this.closeModal()
          },
        },
      )

      this.commentEditDisabled = false
    },
    closeModal() {
      this.editDialog = false
    },
  },
}
</script>
