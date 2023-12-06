<script setup>
const props = defineProps({
    errors: {
        type: Object,
        default: null,
    },
    fieldName: {
        type: String,
        default: "",
    }
})
</script>

<template>
    <div v-if="props.errors ? props.errors[props.fieldName] : false" class="validation-error">
        {{ errors[props.fieldName][0] || '' }}
    </div>
</template>
  

<style scoped>
.validation-error {
    color: red;
    font-size: 0.85rem;
}
</style>