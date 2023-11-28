(function () {
	var root = this;

	// Save the previous value of the '__szzz' variable.
	var previous__szzz = root.__szzz;

	function __szzz(set, ref = false) {
		if (set) {
			if (window === this) {
				return new __szzz(set, ref);
			}
			this.node = ('string' == typeof set) ? ((ref) ? (ref.querySelectorAll(set)) : (document.querySelectorAll(set))) : ((undefined === set.length) ? [set] : set);
			return this;
		} else {
			if (window === this) {
				return new __szzz(set);
			}
			return this;
		}
	}
	root.__szzz = __szzz;

	__szzz.noConflict = function () {
		root.__szzz = previous__szzz;
		return this;
	};
	__szzz.prototype = {
		removeClass: function () {
			for (var i = 0; i < this.node.length; i++) {
				for (var j = 0; j < arguments.length; j++) {
					this.node[i].classList.remove(arguments[j]);
				}
			}
			return this;
		},
		addClass: function () {
			for (var i = 0; i < this.node.length; i++) {
				for (var j = 0; j < arguments.length; j++) {
					this.node[i].classList.add(arguments[j]);
				}
			}
			return this;
		},
		whenInViewbox: function (callback = false) {
			if ('IntersectionObserver' in window) {

				let config = {
					root: null,
					rootMargin: '500px',
					threshold: 0
				};

				if (arguments[1] && typeof arguments[1] === 'object') {
					var prop;
					for (prop in arguments[1]) {
						if (config.hasOwnProperty(prop)) {
							config[prop] = arguments[1][prop];
						}
					}
				}
				let observer = new IntersectionObserver((changes, observer) => {
					changes.forEach(change => {
						if (change.intersectionRatio > 0) {
							if (callback && callback instanceof Function) {
								callback(change.target);
								observer.unobserve(change.target);
							}
						}
					});
				}, config);
				for (i = 0; i < this.node.length; i++) {
					observer.observe(this.node[i]);
				}
			} else {
				// IntersectionObserver NOT Supported
				if (callback && callback instanceof Function) {
					this.node.forEach(node => callback(node));
				}
			}
			return this;
		},
		importModule: function (modUrl = null, type = 'script') {
			if ('' !== modUrl && null !== modUrl && undefined !== modUrl) {
				if ('stylesheet' == type || 'css' == type || 'style' == type) {
					tagName = 'link';
					sourceAttrName = 'href';
					rel = 'stylesheet';
					type = 'text/css';
				}
				if ('js' == type || 'script' == type) {
					tagName = 'script';
					sourceAttrName = 'src';
					rel = '';
					type = 'text/javascript';
				}
				return new Promise(resolve => {
					node = document.createElement(tagName);
					node.setAttribute(sourceAttrName, modUrl);
					node.setAttribute('rel', rel);
					node.setAttribute('type', type);
					// document.body.appendChild(node);
					document.getElementsByTagName("head")[0].appendChild(node);
					node.onload = function () {
						resolve(true);
					};
				});
			} else {
				console.error('ImportModule expects a link to the module.');
			}
		},
	}
}).call(this);


const dope = () => {

	('complete' != document.readyState) && setTimeout(dope, 1);
	if ('loading' == document.readyState) {
		// wLaz_Loaders();
	}
};

window.addEventListener('load', () => {

	console.log('on = ' + document.readyState);
	const RESOURCES = window.szzzLazifyData;
	const styles = RESOURCES.styles;
	const scripts = RESOURCES.scripts;

	var selectorSet = {'styles':[],'scripts':[]};
	
	var szzzDataSet = ( a = '', b = '' ) => {
		identifier = b.replace( /\.|\#|\s|\,|\+|\*|\~|\=|\>|\[|\]/g, '_' );
		identifier = 'szzzLazy_style_' + identifier;
		return {'observed':a, 'selector':b, 'identifier':identifier};
	}
	
	styles.forEach( ( res, idx ) => {
		if( undefined !== res.DOM_selector ) {
			obSelector = ( undefined !== res.observed_selector ) ? res.observed_selector : res.DOM_selector;

			selectorSet.styles.push( szzzDataSet( obSelector, res.DOM_selector ) );

			__szzz( obSelector ).whenInViewbox( ( elem ) => {

				selectorSet.styles.forEach( ( sel ) => {
					if( elem.matches( sel.observed ) ) {
						$a = document.getElementById(sel.identifier);
						( undefined != $a ) && ( $a.parentElement.removeChild( $a ) );
					}
				} );
				
				__szzz().importModule( res.source, 'style' ).then((res) => {
				});
			});
		} else {
			__szzz().importModule( res.source, 'style').then((res) => {});
		}
	} );

	scripts.forEach( ( res, idx ) => {
		if( undefined !== res.DOM_selector ) {
			obSelector = ( undefined !== res.observed_selector ) ? res.observed_selector : res.DOM_selector;

			selectorSet.scripts.push( szzzDataSet( obSelector, res.DOM_selector ) );

			__szzz( obSelector ).whenInViewbox((elem) => {

				selectorSet.scripts.forEach( ( sel ) => {
					if( elem.matches( sel.observed ) ) {
						$a = document.getElementById(sel.identifier);
						( undefined != $a ) && ( $a.parentElement.removeChild( $a ) );
					}
				} );

				__szzz().importModule( res.source, 'script'  ).then((res) => {
				});
			});
		} else {
			__szzz().importModule( res.source, 'script').then((res) => {});
		}
	} );
});
