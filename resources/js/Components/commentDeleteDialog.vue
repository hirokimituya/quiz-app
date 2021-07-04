<template>
	<v-dialog
		v-model="deleteDialog"
		max-width="500"
	>
		<v-card>
			<v-card-title>
				コメント削除
			</v-card-title>
			<v-card-text>
				<div class="mb-3">
					コメントを完全に削除しますか？
				</div>
			</v-card-text>
			
			<v-card-actions class="mt-n5 grey lighten-4">
				<v-spacer></v-spacer>
				<v-btn
					class="my-1"
					@click="closeModal"
				>
					キャンセル
				</v-btn>
				<v-btn
					color="red"
					class="white--text my-1"
					@click="deleteComment"
					:disabled="commentDeleteDisabled"
				>
					コメント削除
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
		quiz_id: {
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
			deleteDialog: false,
			commentDeleteDisabled: false,
		}
	},
	methods: {
		deleteComment() {
			this.commentDeleteDisabled = true

			this.$inertia.delete(route('quiz.comment.edit', {
        quiz: this.quiz_id,
				comment: this.comment.id,
      }), {
				preserveScroll: true,
				onSuccess: () => {
					this.closeModal()
				}
			})

			this.commentDeleteDisabled = false
		},
		closeModal() {
			this.deleteDialog = false
		},
	},
	watch: {
		value: function(val) {
			this.deleteDialog = val
		},
		deleteDialog: function(val) {
			this.$emit('input', val)
		}
	}
}
</script>
