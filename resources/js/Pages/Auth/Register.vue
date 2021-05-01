<template>
  <app-layout>
		<v-card max-width="550" class="mx-auto px-sm-16 mt-md-10 pb-6">
			<v-card-title
				class="primary--text text-h5 d-block text-center"
			>
				クイズメーカーにログイン
			</v-card-title>

			<v-card-text>
				<!-- バリデーションエラー -->
				<alert-validation></alert-validation>

				<v-form>
          <v-text-field
						label="ユーザ名"
						type="text"
						required
						autofocus
            autocomplete="name"
						v-model="form.name"
					></v-text-field>

					<v-text-field
						label="メールアドレス"
						type="email"
						required
						autocomplete="email"
						v-model="form.email"
					></v-text-field>

					<v-text-field
						label="パスワード"
						type="password"
						required 
						autocomplete="new-password"
						v-model="form.password"
					></v-text-field>

          <v-text-field
						label="パスワード（確認用）"
						type="password"
						required 
						autocomplete="new-password"
						v-model="form.password_confirmation"
					></v-text-field>

					<v-btn 
						type="submit"
						block
						color="primary darken-2"
						class="white--text mb-5 mt-5"
						:disabled="form.processing"
						@click.prevent="submit()"
					>
						登録
					</v-btn>

				</v-form>
			</v-card-text>
		</v-card>
	</app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import AlertValidation from '@/Components/AlertValidation'

export default {
	components: {
		AppLayout,
    AlertValidation,
	},
	data() {
		return {
			form: this.$inertia.form({
				name: '',
				email: '',
				password: '',
				password_confirmation: '',
				terms: false,
			})
		}
	},
	methods: {
		submit() {
			this.form.post(this.route('register'), {
				onFinish: () => this.form.reset('password', 'password_confirmation'),
			})
		}
	}
}
</script>
