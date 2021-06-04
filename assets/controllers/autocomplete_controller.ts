import { Controller } from 'stimulus';

interface IUser {
    id: string;
    firstname: string;
    name: string;
    nickname: string;
    profilePicture?: string;
}

export default class extends Controller {
    static values = {
        list: String,
    };

    static targets = ['input', 'suggestions'];

    inputTarget!: HTMLInputElement;
    inputTargets!: HTMLInputElement[];
    hasInputTarget!: boolean;

    suggestionsTarget!: HTMLInputElement;
    suggestionsTargets!: HTMLInputElement[];
    hasSuggestionsTarget!: boolean;

    fetchUrl!: string|null;
    select: HTMLSelectElement|null = null;
    listValue!: String;

    initialize() {
        this.fetchUrl = this.element.getAttribute('data-fetch-url');

        const selectId = this.element.getAttribute('data-select-id');
        if (selectId) {
            this.select = document.getElementById(selectId) as HTMLSelectElement|null;
        }
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
            const a = document.createElement('a');

            a.setAttribute('href', '#');
            a.dataset.userId = user.id;
            a.innerHTML = `
                <img src="{{ uploads/profile-pictures/${user.profilePicture} }}" alt="...">
                <span>${user.firstname} ${user.name}</span>
            `;

            li.appendChild(a);
            this.suggestionsTarget.append(li);
        });
    }

    selectElement(event: MouseEvent) {
        let target = event.target as HTMLAnchorElement;
        if (target.tagName !== 'A') {
            const a = target.closest('a');
            if (a) {
                target = a;
            }
        }

        this.select?.querySelectorAll('option').forEach(option => {
            if (option.value === target.dataset.userId) {
                option.setAttribute('selected', 'selected');
            }
        });

        this.suggestionsTarget.innerHTML = '';
        this.inputTarget.value = '';
    }

    sendElement(event: MouseEvent) {
        const sendUrl = this.suggestionsTarget.dataset.sendUrl;

        let target = event.target as HTMLAnchorElement;
        if (target.tagName !== 'A') {
            const a = target.closest('a');
            if (a) {
                target = a;
            }
        }

        if (sendUrl) {
            fetch(sendUrl, {
                method: 'PATCH',
                body: JSON.stringify({
                    user: target.dataset.userId,
                }),
            })
                .then(response => response.json())
                .then((user: IUser) => {
                    const li = document.createElement('li');
                    li.classList.add('assignee');

                    li.dataset.userId = user.id;
                    li.innerText = user.nickname;

                    const list = document.querySelector(`${this.listValue}`);

                    list?.appendChild(li);

                    this.suggestionsTarget.innerHTML = '';
                })
        }
    }
}
