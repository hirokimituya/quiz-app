<template>
	<app-layout>
		<div v-if="$page.props.jetstream.canUpdateProfileInformation">
      <update-profile-information-form :user="$page.props.user" class="my-8" />
    </div>

		<v-divider></v-divider>

    <div v-if="$page.props.jetstream.canUpdatePassword">
      <update-password-form class="my-8" />
    </div>

		<v-divider></v-divider>

    <div v-if="$page.props.jetstream.canUpdatePassword">
      <show-grade-form class="my-8" />
    </div>

		<v-divider></v-divider>
		
    <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
      <delete-user-form class="my-8" />
    </template>
	</app-layout>
</template>

<script>
	import AppLayout from '@/Layouts/AppLayout'
	import DeleteUserForm from './DeleteUserForm'
	import UpdatePasswordForm from './UpdatePasswordForm'
	import UpdateProfileInformationForm from './UpdateProfileInformationForm'
	import ShowGradeForm from './ShowGradeForm.vue'

	export default {
		props: ['sessions'],

		components: {
			AppLayout,
			DeleteUserForm,
			UpdatePasswordForm,
			UpdateProfileInformationForm,
			ShowGradeForm,
		},
	}
</script>
