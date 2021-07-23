<template>
  <v-row>
    <v-col cols="12" md="4">
      <div class="text-h6">
        <slot name="title"></slot>
      </div>
      <div class="text-subtitle-2 mt-3">
        <slot name="description"></slot>
      </div>
    </v-col>

    <v-col cols="12" md="8">
      <v-card color="accent lighten-5">
        <v-card-text class="px-7 pt-7">
          <form @submit.prevent="$emit('submitted')">
            <slot name="form"></slot>

            <v-card-actions v-if="hasActions">
              <v-spacer></v-spacer>
              <slot name="actions"></slot>
            </v-card-actions>
          </form>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
export default {
  computed: {
    hasActions() {
      return !!this.$slots.actions
    },
  },
}
</script>
