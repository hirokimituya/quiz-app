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
						label="メールアドレス"
						type="email"
						required
						autofocus
						autocomplete="email"
						v-model="form.email"
					></v-text-field>

					<v-text-field
						label="パスワード"
						type="password"
						required 
						autocomplete="current-password"
						v-model="form.password"
					></v-text-field>

					<v-checkbox
						class="mt-n2"
						v-model="form.remember"
						label="次回から自動ログイン"
					></v-checkbox>

					<v-btn 
						type="submit"
						block
						color="primary darken-2"
						class="white--text mb-5"
						:disabled="form.processing"
						@click.prevent="submit()"
					>
						ログイン
					</v-btn>

					<v-btn
						block
						outlined
						color="primary darken-2"
						@click.prevent="pwForget()"
					>
						パスワードを忘れた場合
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
	props: {
		canResetPass: Boolean,
		status: String,
	},
	data() {
		return {
			form: this.$inertia.form({
				email: '',
				password: '',
				remember: false
			})
		}
	},
	methods: {
		submit() {
			this.form
				.transform(data => ({
						... data,
						remember: this.form.remember ? 'on' : ''
				}))
				.post(this.route('login'), {
						onFinish: () => this.form.reset('password'),
				})
		},
		pwForget() {
			this.$inertia.get(route('password.request'))
		}
	}
}
</script>
