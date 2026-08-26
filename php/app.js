document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.card, .category-row a, .auth-hero, .profile-hero').forEach((element, index) => {
        element.style.setProperty('--delay', `${index * 45}ms`);
        element.classList.add('reveal');
    });

    document.querySelectorAll('.category-row a').forEach((category) => {
        category.addEventListener('click', () => category.classList.add('selected'));
    });

    const categorySelect = document.querySelector('.search select');
    if (categorySelect) {
        categorySelect.addEventListener('change', () => categorySelect.form?.requestSubmit());
    }

    document.querySelectorAll('.auto-submit').forEach((control) => {
        control.addEventListener('change', () => control.form?.requestSubmit());
    });

    document.querySelectorAll('.offer-card button').forEach((remove) => {
        remove.addEventListener('click', () => {
            const card = remove.closest('.offer-card');
            const prompt = document.querySelector('.choose-item');
            card?.remove();
            if (prompt) {
                prompt.style.display = 'block';
            }
        });
    });

    document.querySelectorAll('.card img').forEach((image) => {
        image.addEventListener('error', () => {
            image.src = 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=700&q=85';
        }, { once: true });
    });

    const currentPage = new URLSearchParams(window.location.search).get('page') || 'home';
    document.querySelectorAll('.bottom-nav a').forEach((link) => {
        const href = new URL(link.href).searchParams.get('page') || 'home';
        link.classList.toggle('active', href === currentPage && !link.classList.contains('add'));
    });
});
