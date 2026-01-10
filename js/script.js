// ============================================
// MENU MÓVEL
// ============================================

const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const navLinks = document.getElementById('navLinks');

function initMobileMenu() {
    if (mobileMenuBtn && navLinks) {
        mobileMenuBtn.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            const icon = mobileMenuBtn.querySelector('i');
            if (navLinks.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
                document.body.style.overflow = 'hidden';
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
                document.body.style.overflow = 'auto';
            }
        });
    }

    // Fechar menu ao clicar em um link
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', function() {
            if (navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                if (mobileMenuBtn) {
                    const icon = mobileMenuBtn.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
                document.body.style.overflow = 'auto';
            }
        });
    });
}

// ============================================
// SLIDER DE DEPOIMENTOS
// ============================================

function initTestimonialSlider() {
    const testimonialSlides = document.querySelectorAll('.testimonial-slide');
    const testimonialDots = document.querySelectorAll('.slider-dot:not(.team-dot)');
    const testimonialPrevBtn = document.querySelector('.slider-prev:not(.team-prev)');
    const testimonialNextBtn = document.querySelector('.slider-next:not(.team-next)');
    
    if (testimonialSlides.length === 0) return;
    
    let currentTestimonialSlide = 0;
    let testimonialSlideInterval;
    
    function showTestimonialSlide(n) {
        testimonialSlides.forEach(slide => slide.classList.remove('active'));
        testimonialDots.forEach(dot => dot.classList.remove('active'));
        
        currentTestimonialSlide = (n + testimonialSlides.length) % testimonialSlides.length;
        
        testimonialSlides[currentTestimonialSlide].classList.add('active');
        if (testimonialDots[currentTestimonialSlide]) {
            testimonialDots[currentTestimonialSlide].classList.add('active');
        }
    }
    
    function startTestimonialAutoSlide() {
        clearInterval(testimonialSlideInterval);
        testimonialSlideInterval = setInterval(() => {
            showTestimonialSlide(currentTestimonialSlide + 1);
        }, 8000);
    }
    
    function stopTestimonialAutoSlide() {
        clearInterval(testimonialSlideInterval);
    }
    
    // Event listeners para os dots de depoimentos
    testimonialDots.forEach(dot => {
        dot.addEventListener('click', function() {
            const slideIndex = parseInt(this.getAttribute('data-slide'));
            showTestimonialSlide(slideIndex);
            startTestimonialAutoSlide();
        });
    });
    
    // Event listeners para os botões de navegação de depoimentos
    if (testimonialPrevBtn) {
        testimonialPrevBtn.addEventListener('click', function() {
            showTestimonialSlide(currentTestimonialSlide - 1);
            startTestimonialAutoSlide();
        });
    }
    
    if (testimonialNextBtn) {
        testimonialNextBtn.addEventListener('click', function() {
            showTestimonialSlide(currentTestimonialSlide + 1);
            startTestimonialAutoSlide();
        });
    }
    
    // Pausar o slider quando o mouse estiver sobre ele
    const testimonialSlider = document.querySelector('.testimonial-slider');
    if (testimonialSlider) {
        testimonialSlider.addEventListener('mouseenter', stopTestimonialAutoSlide);
        testimonialSlider.addEventListener('mouseleave', startTestimonialAutoSlide);
    }
    
    // Inicializar o slider de depoimentos
    showTestimonialSlide(0);
    startTestimonialAutoSlide();
}

// ============================================
// SLIDER DA EQUIPE - TOTALMENTE RESPONSIVO
// ============================================

function initTeamSlider() {
    const teamSlides = document.querySelectorAll('.team-slide');
    const teamDots = document.querySelectorAll('.team-dot');
    const teamPrevBtn = document.querySelector('.team-prev');
    const teamNextBtn = document.querySelector('.team-next');
    
    if (teamSlides.length === 0) return;
    
    let currentTeamSlide = 0;
    let teamSlideInterval;
    
    function showTeamSlide(n) {
        teamSlides.forEach(slide => slide.classList.remove('active'));
        teamDots.forEach(dot => dot.classList.remove('active'));
        
        currentTeamSlide = (n + teamSlides.length) % teamSlides.length;
        
        teamSlides[currentTeamSlide].classList.add('active');
        if (teamDots[currentTeamSlide]) {
            teamDots[currentTeamSlide].classList.add('active');
        }
    }
    
    function startTeamAutoSlide() {
        clearInterval(teamSlideInterval);
        teamSlideInterval = setInterval(() => {
            showTeamSlide(currentTeamSlide + 1);
        }, 7000); // Troca a cada 7 segundos
    }
    
    function stopTeamAutoSlide() {
        clearInterval(teamSlideInterval);
    }
    
    // Event listeners para os dots da equipe
    teamDots.forEach(dot => {
        dot.addEventListener('click', function() {
            const slideIndex = parseInt(this.getAttribute('data-slide'));
            showTeamSlide(slideIndex);
            startTeamAutoSlide();
        });
    });
    
    // Event listeners para os botões de navegação da equipe
    if (teamPrevBtn) {
        teamPrevBtn.addEventListener('click', function() {
            showTeamSlide(currentTeamSlide - 1);
            startTeamAutoSlide();
        });
    }
    
    if (teamNextBtn) {
        teamNextBtn.addEventListener('click', function() {
            showTeamSlide(currentTeamSlide + 1);
            startTeamAutoSlide();
        });
    }
    
    // Pausar o slider quando o mouse estiver sobre ele
    const teamSliderContainer = document.querySelector('.team-slider-container');
    if (teamSliderContainer) {
        teamSliderContainer.addEventListener('mouseenter', stopTeamAutoSlide);
        teamSliderContainer.addEventListener('mouseleave', startTeamAutoSlide);
    }
    
    // Suporte para touch/swipe em dispositivos móveis
    let touchStartX = 0;
    let touchEndX = 0;
    
    if (teamSliderContainer) {
        teamSliderContainer.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        
        teamSliderContainer.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleTeamSwipe();
        }, { passive: true });
        
        function handleTeamSwipe() {
            const swipeThreshold = 50; // Mínimo de pixels para considerar swipe
            
            if (touchEndX < touchStartX - swipeThreshold) {
                // Swipe para a esquerda = próximo slide
                showTeamSlide(currentTeamSlide + 1);
                startTeamAutoSlide();
            }
            
            if (touchEndX > touchStartX + swipeThreshold) {
                // Swipe para a direita = slide anterior
                showTeamSlide(currentTeamSlide - 1);
                startTeamAutoSlide();
            }
        }
    }
    
    // Inicializar o slider da equipe
    showTeamSlide(0);
    startTeamAutoSlide();
}

// ============================================
// HEADER SCROLL EFFECT
// ============================================

function initHeaderScroll() {
    const header = document.querySelector('header');
    
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                header.style.boxShadow = '0 5px 20px rgba(0, 0, 0, 0.1)';
                header.style.padding = '0.5rem 0';
            } else {
                header.style.boxShadow = '0 2px 15px rgba(0, 0, 0, 0.05)';
                header.style.padding = '1.2rem 0';
            }
        });
    }
}

// ============================================
// SUAVIZAR ROLAGEM PARA ÂNCORAS
// ============================================

function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                const header = document.querySelector('header');
                const headerHeight = header ? header.offsetHeight : 0;
                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// ============================================
// ANIMAÇÃO DE ENTRADA DOS ELEMENTOS
// ============================================

function initScrollAnimations() {
    const animateOnScroll = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    // Aplicar observador a todos os cards
    document.querySelectorAll('.service-card, .space-card, .team-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        animateOnScroll.observe(card);
    });
}

// ============================================
// ANIMAÇÃO DO ARCO-ÍRIS
// ============================================

function initRainbowAnimation() {
    const stripes = document.querySelectorAll('.rainbow-stripe');
    const container = document.querySelector('.services-container');

    if (stripes.length > 0 && container) {
        stripes.forEach(stripe => {
            const length = stripe.getTotalLength();
            stripe.style.strokeDasharray = length;
            stripe.style.strokeDashoffset = length;
        });

        function updatePathOnScroll() {
            const containerTop = container.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            
            const startPoint = windowHeight * 0.2; 
            
            let scrollPercentage = (startPoint - containerTop) / (container.offsetHeight + 100);
            
            scrollPercentage = Math.max(0, Math.min(1, scrollPercentage));

            stripes.forEach(stripe => {
                const length = stripe.getTotalLength();
                const drawLength = length * (2 - scrollPercentage);
                stripe.style.strokeDashoffset = drawLength;
            });
        }

        window.addEventListener('scroll', updatePathOnScroll);
        // Chama a função uma vez para configurar o estado inicial
        updatePathOnScroll();
    }
}

// ============================================
// POP-UPS DOS ELEMENTOS FLUTUANTES
// ============================================

const popupData = {
    'puzzle-piece': {
        title: 'Quebra-Cabeça',
        image: 'imagens/quebracabeca.png',
        text: 'O uso do quebra-cabeça como símbolo do autismo tem fundamento histórico e teórico ligado à forma como o TEA foi compreendido pela ciência ao longo do século XX. Segundo Frith, em Autism: Explaining the Enigma, o autismo era visto como uma condição complexa, heterogênea e de difícil compreensão clínica, o que favoreceu a metáfora do "enigma". Essa ideia também aparece em Wing, ao descrever o espectro como um conjunto amplo de manifestações comportamentais e cognitivas. O símbolo foi criado em 1963 pela National Autistic Society (Reino Unido) justamente para representar essa complexidade.'
    },
    'ribbon': {
        title: 'Fita do Quebra-Cabeça',
        image: 'imagens/fita.png',
        text: 'A "fita do autismo" geralmente se refere à fita do quebra-cabeça colorido, que simboliza a diversidade e complexidade do Transtorno do Espectro Autista (TEA) e a conscientização social, mas tem sido substituída por símbolos mais modernos como o cordão de girassol, que identifica pessoas com deficiências ocultas (incluindo autismo) para facilitar atendimento prioritário e suporte, e o laço do infinito colorido, que celebra a neurodiversidade.'
    },
    'heart': {
        title: 'Cuidado Integral',
        image: 'imagens/psicologia.png',
        text: 'Oferecemos acompanhamento emocional e suporte familiar, entendendo que cada criança é única e merece atenção especializada.'
    },
    'brain': {
        title: 'Desenvolvimento Cognitivo',
        image: 'imagens/psicopedagogia.png',
        text: 'Trabalhamos o desenvolvimento cognitivo através de técnicas inovadoras que estimulam a aprendizagem e o crescimento intelectual.'
    }
};

function initFloatingElementPopups() {
    const popupOverlay = document.getElementById('popupOverlay');
    const popupClose = document.getElementById('popupClose');
    const popupImage = document.getElementById('popupImage');
    const popupTitle = document.getElementById('popupTitle');
    const popupText = document.getElementById('popupText');
    const floatingElements = document.querySelectorAll('.float-element');

    // Adicionar evento de clique para cada elemento flutuante
    floatingElements.forEach(element => {
        element.style.cursor = 'pointer';
        element.style.pointerEvents = 'auto';
        
        element.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const iconElement = this.querySelector('i');
            if (!iconElement) return;
            
            const iconClass = iconElement.className.replace('fas fa-', '');
            
            const data = popupData[iconClass];
            if (!data) return;
            
            popupImage.src = data.image;
            popupImage.alt = data.title;
            popupTitle.textContent = data.title;
            popupText.textContent = data.text;
            
            popupOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    // Fechar pop-up ao clicar no botão X
    if (popupClose) {
        popupClose.addEventListener('click', function() {
            popupOverlay.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
    }

    // Fechar pop-up ao clicar fora do conteúdo
    if (popupOverlay) {
        popupOverlay.addEventListener('click', function(e) {
            if (e.target === popupOverlay) {
                popupOverlay.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
    }

    // Fechar pop-up com a tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && popupOverlay && popupOverlay.classList.contains('active')) {
            popupOverlay.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    });
}

// ============================================
// ANO ATUAL NO FOOTER
// ============================================

function initCurrentYear() {
    const yearSpans = document.querySelectorAll('.current-year');
    if (yearSpans.length > 0) {
        yearSpans.forEach(span => {
            span.textContent = new Date().getFullYear();
        });
    }
}

// ============================================
// EFEITO DE CLIQUE NOS BOTÕES
// ============================================

function initButtonRipple() {
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.5);
                transform: scale(0);
                animation: ripple-animation 0.6s linear;
                width: ${size}px;
                height: ${size}px;
                top: ${y}px;
                left: ${x}px;
            `;
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Adicionar estilo CSS para a animação do ripple
    if (!document.querySelector('style[data-ripple]')) {
        const style = document.createElement('style');
        style.setAttribute('data-ripple', 'true');
        style.textContent = `
            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
            
            .btn {
                position: relative;
                overflow: hidden;
            }
        `;
        document.head.appendChild(style);
    }
}

// ============================================
// ANIMAÇÃO DOS CARDS DE ABORDAGENS
// ============================================

function initApproachCardsAnimation() {
    const approachCards = document.querySelectorAll('.approach-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                observer.unobserve(entry.target);
            }
        });
    }, { 
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    approachCards.forEach(card => {
        observer.observe(card);
    });
}

// ============================================
// ESTEIRA INFINITA - PROFISSIONAIS
// ============================================

function initInfiniteMarquee() {  
    const track = document.querySelector('.marquee-track');  
    if (!track) return;  

    // 1. Clona o conteúdo original para criar o loop infinito  
    const originalContent = track.innerHTML;  
    track.innerHTML = originalContent + originalContent;  

    // 2. Ajusta a velocidade baseada na largura total  
    const totalCards = track.querySelectorAll('.team-marquee-card').length;  
    const baseSpeed = 40;  
    const scrollSpeed = Math.max(15, Math.min(60, baseSpeed * (totalCards / 16)));  
    
    let finalSpeed = scrollSpeed;  
    if (window.innerWidth <= 360) finalSpeed = 15;  
    else if (window.innerWidth <= 480) finalSpeed = 20;  
    else if (window.innerWidth <= 768) finalSpeed = 25;  
    else if (window.innerWidth <= 992) finalSpeed = 30;  
    else if (window.innerWidth <= 1200) finalSpeed = 35;  
    
    track.style.animationDuration = `${finalSpeed}s`;  

    // 3. CONTROLE DE PAUSA/CONTINUAÇÃO - CORRIGIDO
    const marqueeInner = document.querySelector('.marquee-inner');  
    
    // Funções auxiliares para controle da animação  
    function pauseMarquee() {  
        if (track) {  
            track.style.animationPlayState = 'paused';  
        }  
    }  

    function resumeMarquee() {  
        if (track) {  
            track.style.animationPlayState = 'running';  
        }  
    }  

    // 🎯 CONTROLE PARA INTERAÇÃO EM TODOS OS DISPOSITIVOS
    
    // Primeiro, vamos adicionar eventos aos overlays das imagens
    const overlays = document.querySelectorAll('.team-marquee-overlay');
    
    overlays.forEach(overlay => {
        // Eventos de mouse (desktop)
        overlay.addEventListener('mouseenter', pauseMarquee);
        overlay.addEventListener('mouseleave', resumeMarquee);
        
        // Eventos de toque (mobile/tablet)
        overlay.addEventListener('touchstart', (e) => {
            e.preventDefault();
            e.stopPropagation();
            pauseMarquee();
        }, { passive: false });
        
        overlay.addEventListener('touchend', (e) => {
            e.preventDefault();
            e.stopPropagation();
            // Aguarda 2 segundos antes de continuar para dar tempo de leitura
            setTimeout(() => {
                resumeMarquee();
            }, 2000);
        }, { passive: false });
    });
    
    // 🖼️ Adicionar eventos diretos nas imagens
    const images = document.querySelectorAll('.team-marquee-img');
    
    images.forEach(img => {
        // Mouse (desktop)
        img.addEventListener('mouseenter', pauseMarquee);
        img.addEventListener('mouseleave', resumeMarquee);
        
        // Touch (mobile)
        img.addEventListener('touchstart', (e) => {
            e.preventDefault();
            e.stopPropagation();
            pauseMarquee();
        }, { passive: false });
        
        img.addEventListener('touchend', (e) => {
            e.preventDefault();
            e.stopPropagation();
            // Aguarda 2 segundos antes de continuar
            setTimeout(() => {
                resumeMarquee();
            }, 2000);
        }, { passive: false });
    });
    
    // 📋 Adicionar eventos nos cards completos
    const cards = document.querySelectorAll('.team-marquee-card');
    
    cards.forEach(card => {
        // Mouse (desktop)
        card.addEventListener('mouseenter', pauseMarquee);
        card.addEventListener('mouseleave', resumeMarquee);
        
        // Touch (mobile)
        let touchTimeout;
        
        card.addEventListener('touchstart', (e) => {
            e.preventDefault();
            e.stopPropagation();
            pauseMarquee();
            
            // Limpa qualquer timeout anterior
            clearTimeout(touchTimeout);
        }, { passive: false });
        
        card.addEventListener('touchend', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            // Aguarda 3 segundos para leitura completa antes de continuar
            clearTimeout(touchTimeout);
            touchTimeout = setTimeout(() => {
                resumeMarquee();
            }, 3000);
        }, { passive: false });
        
        card.addEventListener('touchmove', (e) => {
            // Se o usuário começar a rolar, continua a esteira
            clearTimeout(touchTimeout);
            resumeMarquee();
        }, { passive: true });
    });
    
    // 🌐 Controle geral no contêiner
    if (marqueeInner) {
        // Mouse (desktop)
        marqueeInner.addEventListener('mouseenter', () => {
            // Pausa só se não estiver sobre um card/imagem específica
            if (!marqueeInner.matches(':hover .team-marquee-card') && 
                !marqueeInner.matches(':hover .team-marquee-img') &&
                !marqueeInner.matches(':hover .team-marquee-overlay')) {
                pauseMarquee();
            }
        });
        
        marqueeInner.addEventListener('mouseleave', () => {
            // Continua só se não estiver sobre um card/imagem específica
            if (!marqueeInner.matches(':hover .team-marquee-card') && 
                !marqueeInner.matches(':hover .team-marquee-img') &&
                !marqueeInner.matches(':hover .team-marquee-overlay')) {
                resumeMarquee();
            }
        });
        
        // Touch (mobile)
        let containerTouchTimeout;
        
        marqueeInner.addEventListener('touchstart', (e) => {
            // Não previne padrão aqui para permitir scroll
            clearTimeout(containerTouchTimeout);
        }, { passive: true });
        
        marqueeInner.addEventListener('touchend', (e) => {
            // Continua após 1 segundo se não tocou em um card específico
            const touchedElement = e.target;
            if (!touchedElement.closest('.team-marquee-card') && 
                !touchedElement.closest('.team-marquee-img') &&
                !touchedElement.closest('.team-marquee-overlay')) {
                
                clearTimeout(containerTouchTimeout);
                containerTouchTimeout = setTimeout(() => {
                    resumeMarquee();
                }, 1000);
            }
        }, { passive: true });
    }
    
    // 4. Recalibrar após todas as imagens carregarem  
    const allImages = track.querySelectorAll('img');  
    if (allImages.length > 0) {  
        let loadedImages = 0;  
        const totalImages = allImages.length;  
        
        allImages.forEach(img => {  
            if (img.complete) {  
                loadedImages++;  
            } else {  
                img.addEventListener('load', () => {  
                    loadedImages++;  
                    if (loadedImages === totalImages) {  
                        track.style.animation = 'none';  
                        void track.offsetWidth;  
                        track.style.animation = `marquee-scroll ${finalSpeed}s linear infinite`;  
                    }  
                });  
            }  
        });  
        
        if (loadedImages === totalImages) {  
            setTimeout(() => {  
                track.style.animation = 'none';  
                void track.offsetWidth;  
                track.style.animation = `marquee-scroll ${finalSpeed}s linear infinite`;  
            }, 100);  
        }  
    }  
}

// ============================================
// ESTEIRA INFINITA DE CONVÊNIOS
// ============================================

function initInfiniteMarqueeConvenios() {  
    const track = document.querySelector('.marquee-track-convenios');  
    if (!track) return;  

    // 1. Clona o conteúdo original para criar o loop infinito  
    const originalContent = track.innerHTML;  
    track.innerHTML = originalContent + originalContent;  

    // 2. Ajusta a velocidade baseada na largura total  
    const totalCards = track.querySelectorAll('.convenio-marquee-card').length;  
    const baseSpeed = 30;  // Mais rápido que os profissionais
    let finalSpeed = baseSpeed;
    
    if (window.innerWidth <= 360) finalSpeed = 12;  
    else if (window.innerWidth <= 480) finalSpeed = 15;  
    else if (window.innerWidth <= 768) finalSpeed = 18;  
    else if (window.innerWidth <= 992) finalSpeed = 22;  
    else if (window.innerWidth <= 1200) finalSpeed = 25;  
    
    track.style.animationDuration = `${finalSpeed}s`;  

    // 3. CONTROLE DE PAUSA/CONTINUAÇÃO
    const marqueeInner = document.querySelector('.marquee-inner-convenios');  
    
    // Funções auxiliares para controle da animação  
    function pauseMarquee() {  
        if (track) {  
            track.style.animationPlayState = 'paused';  
        }  
    }  

    function resumeMarquee() {  
        if (track) {  
            track.style.animationPlayState = 'running';  
        }  
    }  

    // Adicionar eventos aos cards
    const cards = document.querySelectorAll('.convenio-marquee-card');  
    
    cards.forEach(card => {  
        // Mouse (desktop)  
        card.addEventListener('mouseenter', pauseMarquee);  
        card.addEventListener('mouseleave', resumeMarquee);  
        
        // Touch (mobile)  
        let touchTimeout;  
        
        card.addEventListener('touchstart', (e) => {  
            e.preventDefault();  
            e.stopPropagation();  
            pauseMarquee();  
            
            clearTimeout(touchTimeout);  
        }, { passive: false });  
        
        card.addEventListener('touchend', (e) => {  
            e.preventDefault();  
            e.stopPropagation();  
            
            clearTimeout(touchTimeout);  
            touchTimeout = setTimeout(() => {  
                resumeMarquee();  
            }, 1000);  
        }, { passive: false });  
    });  
    
    // 4. Recalibrar após todas as imagens carregarem  
    const allImages = track.querySelectorAll('img');  
    if (allImages.length > 0) {  
        let loadedImages = 0;  
        const totalImages = allImages.length;  
        
        allImages.forEach(img => {  
            if (img.complete) {  
                loadedImages++;  
            } else {  
                img.addEventListener('load', () => {  
                    loadedImages++;  
                    if (loadedImages === totalImages) {  
                        track.style.animation = 'none';  
                        void track.offsetWidth;  
                        track.style.animation = `marquee-scroll-convenios ${finalSpeed}s linear infinite`;  
                    }  
                });  
            }  
        });  
        
        if (loadedImages === totalImages) {  
            setTimeout(() => {  
                track.style.animation = 'none';  
                void track.offsetWidth;  
                track.style.animation = `marquee-scroll-convenios ${finalSpeed}s linear infinite`;  
            }, 100);  
        }  
    }  
}

// ============================================
// INICIALIZAR TUDO QUANDO O DOM CARREGAR
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    initHeaderScroll();
    initSmoothScroll();
    initScrollAnimations();
    initTestimonialSlider();
    initTeamSlider();
    initRainbowAnimation();
    initFloatingElementPopups();
    initCurrentYear();
    initButtonRipple();
    initApproachCardsAnimation();
    
    // Inicializar esteiras infinitas
    setTimeout(() => {
        initInfiniteMarquee(); // Para profissionais
        initInfiniteMarqueeConvenios(); // Para convênios
    }, 300);
    
    // Ajustar velocidade ao redimensionar
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            // Para convênios
            const trackConvenios = document.querySelector('.marquee-track-convenios');
            if (trackConvenios) {
                let newSpeed;
                if (window.innerWidth <= 360) newSpeed = 12;
                else if (window.innerWidth <= 480) newSpeed = 15;
                else if (window.innerWidth <= 768) newSpeed = 18;
                else if (window.innerWidth <= 992) newSpeed = 22;
                else if (window.innerWidth <= 1200) newSpeed = 25;
                else newSpeed = 30;
                
                trackConvenios.style.animationDuration = `${newSpeed}s`;
            }
            
            // Para profissionais
            const trackProfissionais = document.querySelector('.marquee-track');
            if (trackProfissionais) {
                let newSpeedProf;
                if (window.innerWidth <= 360) newSpeedProf = 15;
                else if (window.innerWidth <= 480) newSpeedProf = 20;
                else if (window.innerWidth <= 768) newSpeedProf = 25;
                else if (window.innerWidth <= 992) newSpeedProf = 30;
                else if (window.innerWidth <= 1200) newSpeedProf = 35;
                else newSpeedProf = 40;
                
                trackProfissionais.style.animationDuration = `${newSpeedProf}s`;
            }
        }, 250);
    });
});