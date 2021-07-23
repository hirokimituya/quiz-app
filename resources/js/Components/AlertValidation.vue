<template>
  <v-alert
    type="error"
    v-show="hasErrors"
    transition="slide-x-transition"
    border="left"
    dense
  >
    <ul>
      <li v-for="(error, key) in errors" :key="key">{{ error }}</li>
    </ul>
  </v-alert>
</template>

<script>
export default {
  computed: {
    errors() {
      return this.$page.props.errors
    },
    hasErrors() {
      return Object.keys(this.errors).length > 0
    },
  },
}
</script>
