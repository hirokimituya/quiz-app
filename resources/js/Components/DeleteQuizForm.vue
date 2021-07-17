<template>
	<div class="d-inline">
		<v-btn
			color="red"
			class="white--text my-3"
			height="40"
			@click.stop="confirmQuizDeletion"
		>
			クイズを削除する
		</v-btn>

		<!-- Delete Account Confirmation Modal -->
		<v-dialog
			v-model="confirmingQuizDeletion"
			max-width="500"
		>
			<v-card>
				<v-card-title>
					クイズ削除
				</v-card-title>

				<v-card-text>
					<div class="mb-3">
						クイズを削除してもよろしいですか？ クイズが削除されると、そのすべてのリソースとデータは完全に削除されます。
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
						@click="deleteQuiz"
						:disabled="quizDeleteDisabled"
					>
						クイズを削除する
					</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>
	</div>
</template>

<script>
export default {
	props: {
    quiz_id: {
      type: Number,
      required: true,
    },
	},
	data() {
		return {
			confirmingQuizDeletion: false,
			quizDeleteDisabled: false,
		}
	},
	methods: {
		confirmQuizDeletion() {
			this.confirmingQuizDeletion = true;
		},
		deleteQuiz() {
			this.quizDeleteDisabled = true

			this.$inertia.delete(route('quiz.detail', {
        quiz: this.quiz_id,
      }), {
				preserveScroll: true,
				onSuccess: () => this.closeModal()
			})

			this.quizDeleteDisabled = false
		},
		closeModal() {
			this.confirmingQuizDeletion = false
		},
	},
}
</script>
