<template>
	<profile-form @submitted="updateShowGrade">
		<template #title>
			クイズ実行履歴表示設定
		</template>

		<template #description>
			クイズ実行履歴を他のユーザに表示するかどうかを設定します。
		</template>

		<template #form>
			<v-switch
				v-model="form.show_grade"
				:label="`クイズ実行履歴:　${form.show_grade ? '表示' : '非表示'}`"
				class="ml-2"
			></v-switch>
		</template>

		<template #actions>
			<span class="text-subtitle-2 mr-2" v-show="form.recentlySuccessful">保存完了</span>

			<v-btn
				type="submit"
				color="black"
				class="white--text"
				:disabled="form.processing"
			>
				保存
			</v-btn>
		</template>
	</profile-form>
</template>

<script>
import ProfileForm from '@/Components/ProfileForm'

export default {
	components: {
		ProfileForm,
	},
	data() {
		return {
			form: this.$inertia.form({
				show_grade: !!this.$page.props.user.show_grade,
			}),
		}
	},
	methods: {
		updateShowGrade() {
			this.form.patch(route('show.grade'), {
				preserveScroll: true,
			});
		},
	},
}
</script>
