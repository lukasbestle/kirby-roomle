<template>
	<div
		class="k-block-type-roomle-configurator-wrapper"
		@dblclick="$emit('open')"
	>
		<k-aspect-ratio class="k-block-type-roomle-configurator-main" ratio="1/1">
			<lbro-lazy-image :src="mainUrl">
				<k-empty icon="roomle" layout="cardlets" :text="$t('roomle.empty')" />
			</lbro-lazy-image>
		</k-aspect-ratio>

		<ul class="k-block-type-roomle-configurator-variants">
			<li
				v-for="(variant, index) in content.variants"
				:key="index"
				class="k-block-type-roomle-configurator-variant"
			>
				<lbro-lazy-image :src="variantToImageUrl(variant)">
					<k-empty
						icon="image"
						layout="cardlets"
						:text="$t('roomle.noRendering')"
					/>
				</lbro-lazy-image>

				<span class="k-block-type-roomle-configurator-labels">
					<strong>{{ variant.title }}</strong>
					<span>{{ variant.subtitle }}</span>
				</span>
			</li>
		</ul>
	</div>
</template>

<script>
export default {
	computed: {
		mainUrl() {
			return this.idToImageUrl(this.content.mainproductid);
		}
	},
	methods: {
		/**
		 * Returns the URL to the perspective image of a
		 * Roomle configuration or item by Roomle ID
		 *
		 * @param {String} id Configuration or item ID
		 * @returns {String|null}
		 */
		idToImageUrl(id) {
			if (!id) {
				return null;
			}

			// more than two colons = configuration, else item
			if (id.split(":").length > 2) {
				return (
					"https://uploads.roomle.com/configurations/" +
					id +
					"/perspectiveImage.png"
				);
			}

			return (
				"https://www.roomle.com/api/v2/items/" + id + "/perspectiveImageHD"
			);
		},

		/**
		 * Returns the URL to the perspective image of a
		 * configured variant
		 *
		 * @param {Object} variant Structure item object
		 * @returns {String|null}
		 */
		variantToImageUrl(variant) {
			if (variant.image[0]) {
				return variant.image[0].url;
			}

			if (variant.productid) {
				return this.idToImageUrl(variant.productid);
			}

			return null;
		}
	}
};
</script>

<style>
.k-block-type-roomle-configurator-wrapper,
.k-block-type-roomle-configurator-variants {
	display: grid;
	grid-template-columns: 1fr 1fr;
	grid-gap: 1rem;
}

.k-block-type-roomle-configurator-wrapper {
	/* Allow variants to be shorter than main item */
	align-items: start;
}

.k-block-type-roomle-configurator-variant {
	/* Align labels to the bottom of the grid cell */
	display: flex;
	flex-direction: column;
	justify-content: space-between;
}

.k-block-type-roomle-configurator-main img,
.k-block-type-roomle-configurator-variant img {
	width: 100%;
}

.k-block-type-roomle-configurator-main .k-empty {
	height: 100%;
}

.k-block-type-roomle-configurator-labels {
	margin-top: 0.5rem;
}

.k-block-type-roomle-configurator-labels * {
	display: block;
}

@media screen and (max-width: 40rem) {
	.k-block-type-roomle-configurator-wrapper {
		display: block;
	}
}
</style>
