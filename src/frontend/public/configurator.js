import RoomleConfiguratorApi from "@roomle/embedding-lib/roomle-configurator-api.es";

/**
 * Configurator
 *
 * @package   Kirby Roomle Plugin
 * @author    Lukas Bestle <project-kirbyroomle@lukasbestle.com>
 * @link      https://github.com/lukasbestle/kirby-roomle
 * @copyright Lukas Bestle
 * @license   https://opensource.org/licenses/MIT
 */
export default class {
	/**
	 * @param {Object} props From `$block->frontendJson()`
	 */
	constructor(props) {
		this.props = props;
		this.variantSelector =
			"#" + props.htmlId + " .roomle-configurator-variants input";

		// override the item from the query param if set
		this.currentId = this.idFromUrl() || props.options.id;
	}

	/**
	 * Loads the Roomle configurator and initializes event listeners
	 */
	async init() {
		// only initialize once
		if (this.configurator) {
			return;
		}

		// handle selecting a different variant
		document.querySelectorAll(this.variantSelector).forEach((input) => {
			input.addEventListener("change", this.onVariantSelect.bind(this));
		});

		// handle browser history changes
		window.addEventListener("popstate", this.onUrlChange.bind(this));

		// initialize the variant selector
		this.updateVariants(this.currentId);

		// override the item in case an ID was extracted from the URL
		const options = this.props.options;
		options.id = this.currentId;

		// initialize the configured or passed product
		this.configurator = await RoomleConfiguratorApi.createConfigurator(
			this.props.configuratorId,
			document.getElementById(this.props.htmlId + "-container"),
			options
		);

		// update the configurator if the variant was changed during loading
		if (this.currentId !== options.id) {
			this.load(this.currentId);
		}

		// handle the "request product" interaction
		if (this.props.targetUrl) {
			this.configurator.ui.callbacks.onRequestProduct =
				this.onRequestProduct.bind(this);
		}
	}

	/**
	 * Extracts the item/configuration from the current URL
	 *
	 * @returns {String|null}
	 */
	idFromUrl() {
		const params = new URLSearchParams(window.location.search);
		return params.get(this.props.htmlId);
	}

	/**
	 * Builds a URL for a specific item or configuration in this block
	 *
	 * @param {String} id Item or configuration ID
	 * @param {Boolean} hash Whether to include the hash of the current block
	 * @returns {URL}
	 */
	idToUrl(id, hash = false) {
		const url = new URL(window.location.href);
		url.searchParams.set(this.props.htmlId, id);

		if (hash === true) {
			url.hash = this.props.htmlId;
		}

		return url;
	}

	/**
	 * Loads a new item or configuration into the configurator
	 *
	 * @param {String} id Item or configuration ID
	 * @returns {Boolean} Whether the configurator was updated
	 */
	async load(id) {
		this.currentId = id;
		this.updateVariants(id);

		if (!this.configurator) {
			// not yet initialized
			return false;
		}

		await this.configurator.ui.loadObject(id);

		return true;
	}

	/**
	 * Handles the user clicking the "request product" button
	 * in the configurator
	 *
	 * @param {String} configurationId ID of the current configuration
	 * @param {Base64Image} image Image of the current configuration
	 * @param {KernelPartList} partlist The part list with all details, grouped, etc.
	 * @param {Price} price Price of the current configuration, either set via setPrice or from Roomle price service
	 * @param {Labels} labels The label of the catalog and the furniture system
	 * @param {RapiConfigurationEnhanced} configuration The data returned from the Roomle backend
	 */
	onRequestProduct(
		configurationId,
		image,
		partlist,
		price,
		labels,
		configuration
	) {
		const data = {
			catalog: configuration.catalog,
			configuratorUrl: this.idToUrl(configurationId, true),
			depth: configuration.depth,
			height: configuration.height,
			id: configurationId,
			label: configuration.label,
			parts: partlist.fullList,
			perspectiveImage: configuration.perspectiveImage,
			rootComponentId: configuration.rootComponentId,
			topImage: configuration.topImage,
			width: configuration.width
		};

		// submit the data as a POST request to the target
		const form = document.createElement("form");
		form.action = this.props.targetUrl;
		form.method = "POST";
		form.style.display = "none";

		const input = document.createElement("input");
		input.name = "roomle-configuration";
		input.value = JSON.stringify(data);
		form.appendChild(input);

		document.body.appendChild(form);
		form.submit();
	}

	/**
	 * Handles a browser history change (e.g. back button)
	 */
	onUrlChange() {
		const id = this.idFromUrl();

		if (id) {
			this.load(id);
		} else {
			// reset to the original product
			this.load(this.props.options.id);
		}
	}

	/**
	 * Handles the user selecting a different variant
	 *
	 * @param {Event} event Browser `change` event
	 */
	onVariantSelect(event) {
		// update the page URL
		const url = this.idToUrl(event.target.value);
		window.history.pushState(null, "", url);

		this.load(event.target.value);
	}

	/**
	 * Updates the variants UI with a new ID
	 * if a variant for that ID exists
	 *
	 * @param {String} id Item or configuration ID
	 */
	updateVariants(id) {
		const oldInput = document.querySelector(this.variantSelector + "[checked]");
		const newInput = document.querySelector(
			this.variantSelector + '[value="' + CSS.escape(id) + '"]'
		);

		if (oldInput) {
			oldInput.removeAttribute("checked");
			oldInput.checked = false;
		}

		if (newInput) {
			newInput.checked = true;
			newInput.setAttribute("checked", "true");
		}
	}
}
