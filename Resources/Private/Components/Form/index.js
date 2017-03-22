let mfLIB;
if (typeof $$ === 'function') {
	mfLIB = $$;
} else if (typeof Gator === 'function') {
	mfLIB = Gator;
} else if (typeof jQuery === 'function') {
	mfLIB = jQuery;
}

if (Element && !Element.prototype.matches) {
	let proto = Element.prototype;
	proto.matches = proto.matchesSelector || proto.mozMatchesSelector || proto.msMatchesSelector || proto.oMatchesSelector || proto.webkitMatchesSelector;
}

class Form {
	constructor(selector, context) {
		if (selector instanceof Form) {
			return selector;
		}

		if (typeof selector === 'string') {
			selector = (context || document.documentElement).querySelectorAll(selector);
		}

		if (selector && selector.nodeName) {
			selector = [selector];
		}

		this.nodes = [];
		this.nodes = Form.slice(selector);
	}

	get length() {
		return this.nodes.length;
	}

	static slice(pseudo) {
		if (!pseudo || pseudo.length === 0 || typeof pseudo === 'string' || pseudo.toString() === '[object Function]') {
			return [];
		}
		return pseudo.length ? [].slice.call(pseudo.nodes || pseudo) : [pseudo];
	}

	each(callback) {
		this.nodes.forEach(callback.bind(this));
		return this;
	}

	static toggle(element, className) {
		element.parentNode.classList[element.value ? 'add' : 'remove'](className);
	}

	trigger(eventName = 'change') {
		return this.each(node => {
			let eventTrigger;
			try {
				eventTrigger = new window.CustomEvent(eventName);
			} catch (error) {
				if (document.createEvent) {
					eventTrigger = document.createEvent('HTMLEvents');
					eventTrigger.initEvent(eventName, true, true);
				} else {
					eventTrigger = document.createEventObject();
					eventTrigger.eventType = eventName;
				}
				eventTrigger.eventName = eventName;
			}

			if (document.createEvent) {
				node.dispatchEvent(eventTrigger);
			} else {
				node.fireEvent('on' + eventTrigger.eventType, eventTrigger);
			}
		});
	}

	val(value) {
		if (typeof value === 'string' || typeof value === 'number') {
			return this.each(element => {
				element.value = value;
				new MF(element).trigger('change');
				new MF(element).trigger('input');
			});
		} else {
			return this.nodes[0].value;
		}
	}

	input(opts, callback = () => {}, time = 2000) {
		let timer;
		let defaults = {
			className: {
				hasValue: 'mf-has-value'
			}
		};
		let settings = $.extend({}, defaults, opts || {});

		return this.each(element => {
			Form.toggle(element, settings.className.hasValue);
			element.addEventListener('input', () => {
				Form.toggle(element, settings.className.hasValue);
				clearTimeout(timer);
				timer = setTimeout(() => {
					callback(element)
				}, time);
			});
		});
	}

	spinner(opts, callback = () => {}, time = 2000) {
		let defaults = {
			className: {
				hasValue: 'mf-has-value',
				spinner: 'mf-spinner',
				up: 'mf-spinner-up',
				down: 'mf-spinner-down'
			}
		};
		let settings = $.extend({}, defaults, opts || {});

		return this.each(element => {
			let spinner = document.createElement('div');
			let up = document.createElement('div');
			let down = document.createElement('div');
			let min = parseInt(element.min);
			let max = parseInt(element.max);
			let step = parseInt(element.step) || 1;

			if (isNaN(min)) {
				min = false;
			}
			if (isNaN(max)) {
				max = false;
			}

			function noValue() {
				if (!element.value && element.value != 0) {
					element.value = 0;
				}
			}

			spinner.className = settings.className.spinner;
			up.className = settings.className.up;
			down.className = settings.className.down;
			spinner.appendChild(up);
			spinner.appendChild(down);
			up.addEventListener('click', () => {
				noValue();
				try {
					element.stepUp();
				} catch (error) {
					let value = parseInt(element.value) || 0;
					if (max !== false && value >= max) {
						value = max;
					} else {
						value += step;
					}
					element.value = value;
				} finally {
					new Form(element).trigger('input');
				}
			});
			down.addEventListener('click', () => {
				noValue();
				try {
					element.stepDown();
				} catch (error) {
					let value = parseInt(element.value) || 0;
					if (min !== false && value <= min) {
						value = min;
					} else {
						value -= step;
					}
					element.value = value;
				} finally {
					new Form(element).trigger('input');
				}
			});
			new Form(element).input(settings, callback, time);
			element.parentNode.appendChild(spinner);
		});
	}

	dropdown(opts, callback = () => {}, time = 500) {
		let timer;
		let defaults = {
			className: {
				hasValue: 'mf-has-value',
				dropdown: 'mf-select-dropdown',
				list: 'mf-select-dropdown-menu',
				right: 'mf-select-dropdown-right',
				button: 'mf-select-btn',
				open: 'mf-select-open',
				select: 'mf-select'
			}
		};
		let settings = $.extend({}, defaults, opts || {});

		function getText(element) {
			let text = element.textContent || element.innerText;
			return text.trim();
		}

		return this.each(element => {
			let parent = element.parentNode;
			let children = new Form(element.children);
			let options = children.nodes;
			let active = options[0];
			let list = '';
			let button = '';
			let dropdown = document.createElement('div');
			let allDropdowns = new Form('.' + settings.className.select);
			let dropdownText;

			if (element.getAttribute('data-sort')) {
				options.sort((a, b) => getText(a) > getText(b));
			}

			createList();
			createButton();
			writeDropdown();
			addHandler();

			function createList() {
				let markup ='<div class="' + settings.className.list + '"><ul>';
				children.each(option => {
					let value = option.getAttribute('value');
					if (option.matches('[selected]')) {
						active = option;
					}
					if (value) {
						markup += '<li data-value="' + value + '">' + getText(option) + '</li>';
					}
				});

				markup += '</ul></div>';
				list = markup;
			}

			function createButton() {
				let text = getText(active) || '&nbsp;';
				let markup = '<button class="' + settings.className.button + '" type="button" aria-haspopup="true" aria-expanded="false"><span class="' +  settings.className.button + '-text">' + text + '</span></button>';
				button = markup;

				if (text !== '&nbsp;') {
					parent.classList.add(settings.className.hasValue);
				}
			}

			function writeDropdown() {
				dropdown.className = settings.className.dropdown;
				dropdown.innerHTML = button + list;
				element.parentNode.insertBefore(dropdown, element);
				element.style.display = 'none';
				dropdownText = dropdown.querySelector('.' + settings.className.button + '-text');
			}

			function closeAllDropdowns() {
				allDropdowns.each(element => {
					element.classList.remove(settings.className.open);
				});
			}

			function addHandler() {
				mfLIB(dropdown).on('focus', 'button', event => {
					event.target.onkeyup = function(event) {
						event.target.click();
					}
				});

				mfLIB(dropdown).on('click', 'button', event => {
					let isOpen = parent.classList.contains(settings.className.open);
					event.stopPropagation();
					closeAllDropdowns();
					if (!isOpen) {
						setTimeout(() => {
							parent.classList.add(settings.className.open);
						}, 10);
					}
				});

				mfLIB(element).on('change', () => {
					let value = this.value;
					dropdown.querySelector('li[data-value="' + value + '"]').click();
				});

				mfLIB(dropdown).on('click', 'li[data-value]', event => {
					event.stopPropagation();
					let value = this.getAttribute('data-value');
					let text = getText(this);
					element.value = value;
					dropdownText.innerHTML = text;
					parent.classList[text ? 'add' : 'remove'](settings.className.hasValue);
					closeAllDropdowns();
					clearTimeout(timer);
					timer = setTimeout(() => { callback(element) }, time);
				});

				// mfLIB(dropdown).on('blur', 'button', event => {
				// 	parent.classList.remove(settings.className.open);
				// });

				mfLIB(document).on('click', () => {
					parent.classList.remove(settings.className.open);
				});
			}
		});
	}

	file(opts) {
		let defaults = {
			selector: {
				input: '.mf-file-field',
				files: '.mf-file-files'
			}
		};
		let settings = $.extend({}, defaults, opts || {});

		return this.each(element => {
			let input = element.querySelector(settings.selector.input);
			let filesList = element.querySelector(settings.selector.files);

			input.addEventListener('change', (event) => {
				writeFilesToList(event.target.files);
			});

			const writeFilesToList = (files) => {
				let list = [];
				filesList.innerHTML = '';
				for (let i = 0; i < files.length; i++) {
					list.push(files.item(i).name);
				}
				filesList.innerHTML = list.join('');
				return list;
			};
		});
	}
}

export let MF = (selector, context) => new Form(selector, context);
