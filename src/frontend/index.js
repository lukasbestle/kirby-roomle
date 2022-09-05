import roomleIcon from "./icons/roomle.svg?raw";
import Configurator from "./components/Configurator.vue";
import LazyImage from "./components/LazyImage.vue";

panel.plugin("lukasbestle/roomle", {
	blocks: {
		"roomle-configurator": Configurator
	},
	components: {
		"lbro-lazy-image": LazyImage
	},
	icons: {
		roomle: roomleIcon
	}
});
