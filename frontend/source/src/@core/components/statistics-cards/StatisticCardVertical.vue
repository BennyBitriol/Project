<template>
  <b-card class="text-center">
    <b-avatar
      class="mb-1"
      :variant="`light-${color}`"
      size="50"
    >
      <feather-icon
        size="30"
        :icon="icon"
      />
    </b-avatar>
    <div class="truncate">
      <h5 class="mb-25 font-weight-bolder">
        {{ statistic }}
      </h5>
      <span>{{ statisticTitle }}</span>
    </div>
  </b-card>
</template>

<script>
import { BCard, BAvatar } from 'bootstrap-vue'

export default {
  components: {
    BCard,
    BAvatar,
  },
  props: {
    icon: {
      type: String,
      required: true,
    },
    statistic: {
      type: [Number, String],
      required: true,
    },
    statisticTitle: {
      type: String,
      default: '',
    },
    color: {
      type: String,
      default: 'primary',
    },
  },
}
</script>
