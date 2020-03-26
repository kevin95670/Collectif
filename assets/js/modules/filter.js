import {Flipper, spring} from 'flip-toolkit'; 

/*
* @property {HTMLElement} pagination
* @property {HTMLElement} contenu
* @property {HTMLFormElement} recherche
*/

export default class Filter {

	/*
	* @param {HTMLElemnt | null} element
	*/
	constructor(element){

		if(element === null){
			return;
		}
		this.pagination = document.querySelector('.js-filter-pagination');
		this.contenu = document.querySelector('.js-filter-content');
		this.recherche = document.querySelector('.js-filter-form');
		this.bindEvents();
	}

	/*
	*	Ajoute les comportements aux différents éléments
	*/
	bindEvents()
	{
		this.recherche.querySelectorAll('input').forEach(input => {
			input.addEventListener('change', this.loadForm.bind(this));
		});

		this.recherche.querySelectorAll('select').forEach(select => {
			select.addEventListener('change', this.loadForm.bind(this));
		});

		this.pagination.addEventListener('click', e => {
			if (e.target.tagName === 'A') {
				e.preventDefault();
				this.loadUrl(e.target.getAttribute('href'));
			}
		})
	}

	async loadForm(){
		const data = new FormData(this.recherche);
		const url = new URL(this.recherche.getAttribute('action') || window.location.href);
		const params = new URLSearchParams();
		data.forEach((value, key) => {
			params.append(key, value)
		});
		return this.loadUrl(url.pathname + '?' + params.toString());
	}

	async loadUrl(url){
		this.showLoader();
		const params = new URLSearchParams(url.split('?')[1] || '');
		params.set('ajax', 1);
		const response = await fetch(url.split('?')[0] + '?' + params.toString(), {
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		});
		if (response.status >= 200 && response.status < 300){
			const data = await response.json();
			this.flipContent(data.contenu);
			this.pagination.innerHTML = data.pagination;
			params.delete('ajax');
			history.replaceState({}, '', url.split('?')[0] + '?' + params.toString());
		}
		else{
			console.error(response);
		}
		this.hideLoader();
	}

	/*
	*	Remplace les éléments de la grille avec une animation
	*/
	flipContent (contenu){
		const exitAnimation = function(element, index, complete){
			spring({
				config: 'stiff',
				values: {
					translateY: [0,-20],
					opacity: [1,0]
				},
				onUpdate: ({ translateY, opacity }) => {
					element.style.opacity = opacity;
					element.style.transform = `translateY(${translateY}px)`;
				},
				onComplete: complete
			})
		}
			const appearAnimation = function(element, index){
			spring({
				config: 'stiff',
				values: {
					translateY: [20,0],
					opacity: [0,1]
				},
				onUpdate: ({ translateY, opacity }) => {
					element.style.opacity = opacity;
					element.style.transform = `translateY(${translateY}px)`;
				},
				delay: index * 15
			})
		}
		const flipper = new Flipper({
			element : this.contenu
		});

		this.contenu.children.forEach(element => {
			flipper.addFlipped({
				element,
				flipId: element.id,
				shouldFlip: false,
				onExit: exitAnimation
			})
		});

		flipper.recordBeforeUpdate();

		this.contenu.innerHTML = contenu;

		this.contenu.children.forEach(element => {
			flipper.addFlipped({
				element,
				flipId: element.id,
				onAppear: appearAnimation 
			})
		});

		flipper.update();
	}

	showLoader(){
		this.recherche.classList.add('is-loading');
		const loader = this.recherche.querySelector('.js-loading');
		if (loader === null) {
			return;
		};

		loader.setAttribute('aria-hidden', 'false');
		loader.style.display = 'block';
	}

	hideLoader(){
		this.recherche.classList.remove('is-loading');
		const loader = this.recherche.querySelector('.js-loading');
		if (loader === null) {
			return;
		};

		loader.setAttribute('aria-hidden', 'true');
		loader.style.display = 'none';
	}

}