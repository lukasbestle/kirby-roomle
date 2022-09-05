<template>
	<span>
		<img
			v-if="src"
			v-show="visible"
			:alt="alt"
			:src="src"
			@error="visible = false"
			@load="visible = true"
		/>
		<slot v-if="!src || !visible" />
	</span>
</template>

<script>
export default {
	props: {
		alt: {
			type: String,
			default: ""
		},
		src: String
	},
	data() {
		return {
			visible: true
		};
	}
};
</script>
