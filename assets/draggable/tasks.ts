export function onDragStart(event: DragEvent) {
    if (!event.dataTransfer) {
        return;
    }

    const target = event.target as HTMLElement;
    event.dataTransfer?.setData('text/plain', target.id);
    event.dataTransfer.dropEffect = 'move';
}

export function onDrop(event: DragEvent) {
    event.preventDefault();
    document.getElementById('dropping-placeholder')?.remove();

    const id = event.dataTransfer?.getData('text/plain');
    // console.log(id);
    if (!id) {
        return;
    }
    const target = event.target as HTMLElement;
    const draggingElement = document.getElementById(id);
    // console.log(target);

    if (!draggingElement) {
        return;
    }

    target.appendChild(draggingElement);
}

export function onDragOver(event: DragEvent) {
    const target = event.target as HTMLElement;
    // console.log(target);
    if (target.classList.contains('dropZone')) {
        event.preventDefault();
    }
}

export function onDragEnter(event: DragEvent) {
    const target = event.target as HTMLElement;
    const id = event.dataTransfer?.getData('text/plain');
    if (!id) {
        return;
    }

    const draggedElement = document.getElementById(id);

    if (!draggedElement) {
        return;
    }

    const placeholder = createPlaceholder(
        draggedElement.getBoundingClientRect().width,
        draggedElement.getBoundingClientRect().height
    );
    target.insertBefore(placeholder, null);
}

export function onDragLeave(event: DragEvent) {
    document.getElementById('dropping-placeholder')?.remove();
}

function createPlaceholder(width: number, height: number): Node {
    const placeholder = document.createElement('div');
    placeholder.id = 'dropping-placeholder';
    placeholder.textContent = 'DROP HERE :)';
    placeholder.style.width = `${width}px`;
    placeholder.style.height = `${height}px`;
    placeholder.style.border = '2px dotted red';

    return placeholder;
}
