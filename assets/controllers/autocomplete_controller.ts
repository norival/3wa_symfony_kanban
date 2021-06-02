import { Controller } from 'stimulus';

interface IUser {
    id: string;
    firstname: string;
    name: string;
    profilePicture?: string;
}

export default class extends Controller {
    static targets = ['input', 'suggestions'];

    inputTarget!: HTMLInputElement;
    inputTargets!: HTMLInputElement[];
    hasInputTarget!: boolean;

    suggestionsTarget!: HTMLInputElement;
    suggestionsTargets!: HTMLInputElement[];
    hasSuggestionsTarget!: boolean;

    fetchUrl!: string|null;

    initialize() {
        this.fetchUrl = this.element.getAttribute('data-fetch-url');
    }

    connect() {
    }

    input() {
        if (!this.fetchUrl) {
            throw new Error('No url found');
        }

        fetch(`${this.fetchUrl}?q=${this.inputTarget.value}`, {
            method: 'GET',
        })
            .then(response => response.json())
            .then(data => this.renderSuggestions(data));
    }

    renderSuggestions(users: IUser[]): void {
        this.suggestionsTarget.innerHTML = '';
        users.forEach(user => {
            const li = document.createElement('li');
            const img = document.createElement('img');
            const span = document.createElement('span');
            li.dataset.userId = user.id;
            span.innerText = `${user.firstname} ${user.name}`;

            if (user.profilePicture) {
                img.setAttribute('src', `uploads/profile-pictures/${user.profilePicture}`);
            }
            img.classList.add('profilePicture', 'thumbnail');
            li.appendChild(img);
            li.appendChild(span);
            this.suggestionsTarget.append(li);
        });
    }
}
