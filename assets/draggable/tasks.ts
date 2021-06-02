export function onDragStart(event: DragEvent) {
    if (!event.dataTransfer) {
        return;
    }

    const target = event.target as HTMLElement;
    event.dataTransfer?.setData('text/plain', target.id);
    event.dataTransfer?.setData('text/html', target.innerHTML);
    event.dataTransfer.dropEffect = 'move';

    setTimeout(() => {
        target.classList.add('hide');
    }, 0);
}

export function onDrop(event: DragEvent) {
    document.getElementById('dropping-placeholder')?.remove();

    const id = event.dataTransfer?.getData('text/plain');
    if (!id) {
        return;
    }

    const target = event.target as HTMLElement;

    if (target.classList.contains('.dropZone')) {
        event.preventDefault();
    }

    const draggedElement = document.getElementById(id);

    if (!draggedElement) {
        return;
    }

    draggedElement.classList.remove('hide');

    const dropTarget = findDropTarget(target);

    if (dropTarget.adjacent) {
        dropTarget.target?.insertBefore(draggedElement, null);

        return;
    }
    console.log('toto');

    dropTarget.target?.appendChild(draggedElement);
}

export function onDragOver(event: DragEvent) {
    const target = event.target as HTMLElement;

    if (target.classList.contains('dropZone')) {
        event.preventDefault();
    }
}

export function onDragEnter(event: DragEvent) {
    document.getElementById('dropping-placeholder')?.remove();

    const target = event.target as HTMLElement;
    const id = event.dataTransfer?.getData('text/plain');
    const html = event.dataTransfer?.getData('text/html');
    const dropTarget = findDropTarget(target);

    if (!id || !html) {
        return;
    }

    const draggedElement = document.getElementById(id);

    if (!draggedElement) {
        return;
    }

    const placeholder = createPlaceholder(html);

    if (dropTarget.adjacent) {
        dropTarget.target?.insertBefore(placeholder, null);

        return;
    }

    dropTarget.target?.appendChild(placeholder);
}

export function onDragLeave(event: DragEvent) {
    document.getElementById('dropping-placeholder')?.remove();
}

function createPlaceholder(html: string): Node {
    const placeholder = document.createElement('div');
    placeholder.id = 'dropping-placeholder';
    placeholder.classList.add('task', 'placeholder');
    placeholder.innerHTML = html;

    return placeholder;
}

function findDropTarget(target: HTMLElement): {target: Element|null; adjacent: boolean} {
    console.log(target);
    let dropTarget = target.closest('.task');
    let adjacent = true;

    if (!dropTarget) {
        dropTarget = target.closest('.boardElement .content');
        adjacent = false
    }

    return {
        target: dropTarget,
        adjacent: adjacent
    };
}
