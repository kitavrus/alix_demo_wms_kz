// Global state
let heroSlideIndex = 0;
let currentFilter = 'all';
let countdownInterval = null;

// Hero slider functionality
const heroSlides = [
  {
    image: 'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?w=800&h=500&fit=crop&crop=center',
    title: 'Семья с новым кроссовером',
    subtitle: 'Акция! Первый взнос 0%'
  },
  {
    image: 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=500&fit=crop&crop=center',
    title: 'Электромобили BYD',
    subtitle: 'Экологично и выгодно'
  },
  {
    image: 'https://images.unsplash.com/photo-1590362891991-f776e747a588?w=800&h=500&fit=crop&crop=center',
    title: 'Внедорожники Chery',
    subtitle: 'Мощь и надежность'
  }
];

function goToSlide(index) {
  heroSlideIndex = index;
  const heroImage = document.getElementById('hero-image');
  const indicators = document.getElementById('hero-indicators').children;
  
  // Update image
  heroImage.src = heroSlides[index].image;
  heroImage.alt = heroSlides[index].title;
  
  // Update indicators
  for (let i = 0; i < indicators.length; i++) {
    indicators[i].className = i === index 
      ? 'w-3 h-3 rounded-full transition-all duration-300 bg-orange-400'
      : 'w-3 h-3 rounded-full transition-all duration-300 bg-white bg-opacity-50';
  }
}

function nextSlide() {
  const nextIndex = (heroSlideIndex + 1) % heroSlides.length;
  goToSlide(nextIndex);
}

// Navigation functions
function toggleMobileMenu() {
  const menu = document.getElementById('mobile-menu');
  const icon = document.getElementById('mobile-menu-icon');
  
  menu.classList.toggle('hidden');
  
  if (menu.classList.contains('hidden')) {
    icon.className = 'fas fa-bars text-gray-700';
  } else {
    icon.className = 'fas fa-times text-gray-700';
  }
}

function scrollToCalculator() {
  document.getElementById('calculator')?.scrollIntoView({ behavior: 'smooth' });
}

function scrollToCatalog() {
  document.getElementById('catalog')?.scrollIntoView({ behavior: 'smooth' });
}

function scrollToFinalForm() {
  document.getElementById('final-cta')?.scrollIntoView({ behavior: 'smooth' });
}

// Car filtering
function filterCars(type) {
  currentFilter = type;
  
  // Update filter buttons
  const buttons = ['all', 'electric', 'hybrid', 'crossover', 'sedan'];
  buttons.forEach(btn => {
    const element = document.getElementById(`filter-${btn}`);
    if (element) {
      if (btn === type) {
        element.className = 'px-6 py-3 rounded-lg font-semibold transition-all duration-300 bg-blue-600 text-white shadow-lg';
      } else {
        element.className = 'px-6 py-3 rounded-lg font-semibold transition-all duration-300 bg-white text-gray-700 hover:bg-gray-100 border border-gray-200';
      }
    }
  });
  
  renderCars();
}

function renderCars() {
  const grid = document.getElementById('cars-grid');
  const filteredCars = getCarsByType(currentFilter);
  const displayCars = filteredCars.slice(0, 16); // Show only 16 cars on main page
  
  grid.innerHTML = displayCars.map(car => `
    <div class="card cursor-pointer group overflow-hidden" onclick="goToCarDetails('${car.id}')">
      <div class="relative overflow-hidden">
        <img src="${car.image}" alt="${car.name}" class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy">
        <div class="absolute top-4 left-4">
          <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
            ${getCarTypeLabel(car.type)}
          </span>
        </div>
        <div class="absolute top-4 right-4">
          <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
            АКЦИЯ
          </span>
        </div>
      </div>
      
      <div class="p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
          ${car.name}
        </h3>
        
        <div class="space-y-2 mb-4">
          <div class="flex justify-between text-sm text-gray-600">
            <span>Двигатель:</span>
            <span class="font-semibold">${car.specifications.engine}</span>
          </div>
          <div class="flex justify-between text-sm text-gray-600">
            <span>Мощность:</span>
            <span class="font-semibold">${car.specifications.power}</span>
          </div>
          <div class="flex justify-between text-sm text-gray-600">
            <span>Привод:</span>
            <span class="font-semibold">${car.specifications.drive}</span>
          </div>
        </div>
        
        <div class="border-t pt-4">
          <div class="text-2xl font-bold text-gray-900 mb-2">
            ${formatPrice(car.price)} ₸
          </div>
          <div class="text-lg font-semibold text-green-600">
            от ${formatPrice(car.monthlyPayment)} ₸/мес
          </div>
          <div class="text-sm text-gray-500 mt-1">
            от ${car.loanOptions.interestRate}% годовых
          </div>
        </div>
        
        <button class="block w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors duration-200 text-center">
          Подробнее
        </button>
      </div>
    </div>
  `).join('');
  
  // Update total cars count
  document.getElementById('total-cars-count').textContent = cars.length;
}

function goToCarDetails(carId) {
  window.location.href = `car-details.html?id=${carId}`;
}

// Calculator functionality
function updatePriceFromRange() {
  const rangeValue = document.getElementById('car-price-range').value;
  document.getElementById('car-price').value = rangeValue;
  updateCalculations();
}

function updateCalculations() {
  const carPrice = parseInt(document.getElementById('car-price').value) || 15000000;
  const downPaymentPercent = parseInt(document.getElementById('down-payment-range').value) || 0;
  const loanTermYears = parseInt(document.getElementById('loan-term-range').value) || 7;
  const interestRate = parseFloat(document.getElementById('interest-rate-range').value) || 5.9;
  
  // Update range values
  document.getElementById('car-price-range').value = carPrice;
  document.getElementById('down-payment-percent').textContent = downPaymentPercent;
  document.getElementById('loan-term-years').textContent = loanTermYears;
  document.getElementById('interest-rate').textContent = interestRate;
  document.getElementById('current-rate').textContent = interestRate;
  
  // Calculate loan parameters
  const downPaymentAmount = Math.round(carPrice * downPaymentPercent / 100);
  const loanAmount = carPrice - downPaymentAmount;
  
  // Calculate monthly payment
  const monthlyRate = interestRate / 100 / 12;
  const numPayments = loanTermYears * 12;
  
  let monthlyPayment;
  if (monthlyRate === 0) {
    monthlyPayment = Math.round(loanAmount / numPayments);
  } else {
    monthlyPayment = Math.round(loanAmount * (monthlyRate * Math.pow(1 + monthlyRate, numPayments)) / 
                    (Math.pow(1 + monthlyRate, numPayments) - 1));
  }
  
  const totalAmount = downPaymentAmount + (monthlyPayment * numPayments);
  const overpayment = totalAmount - carPrice;
  
  // Update display
  document.getElementById('down-payment-amount').value = formatPrice(downPaymentAmount);
  document.getElementById('monthly-payment').textContent = formatPrice(monthlyPayment) + ' ₸';
  document.getElementById('overpayment').textContent = formatPrice(overpayment) + ' ₸';
  document.getElementById('total-amount').textContent = formatPrice(totalAmount) + ' ₸';
  
  // Update years text
  const yearsText = loanTermYears === 1 ? 'год' : loanTermYears >= 2 && loanTermYears <= 4 ? 'года' : 'лет';
  document.getElementById('loan-term-text').textContent = yearsText;
  
  // Update chart
  drawPaymentChart(monthlyPayment, numPayments);
}

function drawPaymentChart(monthlyPayment, numPayments) {
  const canvas = document.getElementById('payment-chart');
  if (!canvas) return;
  
  const ctx = canvas.getContext('2d');
  
  // Set canvas size
  canvas.width = canvas.offsetWidth * 2;
  canvas.height = canvas.offsetHeight * 2;
  ctx.scale(2, 2);
  
  const width = canvas.offsetWidth;
  const height = canvas.offsetHeight;
  
  // Clear canvas
  ctx.clearRect(0, 0, width, height);
  
  // Chart data - like in Vue version
  const months = numPayments;
  const maxPayment = monthlyPayment * 1.2;
  const barWidth = (width - 80) / months;
  
  // Draw bars
  for (let i = 0; i < months; i++) {
    const x = 40 + i * barWidth;
    const barHeight = (monthlyPayment / maxPayment) * (height - 60);
    const y = height - 30 - barHeight;
    
    // Gradient - same as Vue version
    const gradient = ctx.createLinearGradient(0, y, 0, y + barHeight);
    gradient.addColorStop(0, '#3B82F6');
    gradient.addColorStop(1, '#10B981');
    
    ctx.fillStyle = gradient;
    ctx.fillRect(x, y, Math.max(barWidth - 1, 1), barHeight);
  }
  
  // Draw axes
  ctx.strokeStyle = '#9CA3AF';
  ctx.lineWidth = 1;
  ctx.beginPath();
  ctx.moveTo(40, height - 30);
  ctx.lineTo(width - 20, height - 30);
  ctx.moveTo(40, 20);
  ctx.lineTo(40, height - 30);
  ctx.stroke();
  
  // Labels
  ctx.fillStyle = '#6B7280';
  ctx.font = '12px Inter';
  ctx.textAlign = 'center';
  ctx.fillText('Месяцы', width / 2, height - 5);
  
  ctx.save();
  ctx.translate(15, height / 2);
  ctx.rotate(-Math.PI / 2);
  ctx.fillText('Платеж (₸)', 0, 0);
  ctx.restore();
}

// Modal functionality
function showApplicationModal() {
  const modal = document.getElementById('application-modal');
  modal.classList.remove('hidden');
  
  // Update modal data with current calculator values
  document.getElementById('modal-car-price').textContent = document.getElementById('car-price').value ? 
    formatPrice(parseInt(document.getElementById('car-price').value)) + ' ₸' : '15 000 000 ₸';
  document.getElementById('modal-monthly-payment').textContent = document.getElementById('monthly-payment').textContent;
  document.getElementById('modal-term').textContent = document.getElementById('loan-term-years').textContent + ' ' + 
    document.getElementById('loan-term-text').textContent;
}

function hideApplicationModal() {
  document.getElementById('application-modal').classList.add('hidden');
}

// FAQ functionality
const faqData = [
  {
    question: 'Нужен ли КАСКО для получения автокредита?',
    answer: `<p>КАСКО не является обязательным требованием для всех кредитов, но:</p>
             <ul class="list-disc ml-6 mt-2 space-y-1">
               <li>При оформлении КАСКО ставка снижается на 1-2%</li>
               <li>Для кредитов свыше 10 млн ₸ КАСКО обязательно</li>
               <li>Мы предоставляем КАСКО в подарок на первый год при ставке до 7%</li>
               <li>Можно выбрать КАСКО частичное (только от угона и ущерба)</li>
             </ul>`
  },
  {
    question: 'Можно ли погасить кредит досрочно?',
    answer: `<p>Да, досрочное погашение возможно без ограничений:</p>
             <ul class="list-disc ml-6 mt-2 space-y-1">
               <li>Без комиссий и штрафов</li>
               <li>Частичное или полное досрочное погашение</li>
               <li>Пересчет процентов в день погашения</li>
               <li>Уведомление банка за 1 день</li>
             </ul>
             <p class="mt-3"><strong>Экономия:</strong> при досрочном погашении на 2 года раньше экономия составит до 15% от общей переплаты.</p>`
  },
  {
    question: 'Какие документы нужны для оформления?',
    answer: `<p>Минимальный пакет документов:</p>
             <ul class="list-disc ml-6 mt-2 space-y-1">
               <li>Удостоверение личности (паспорт или ID-карта)</li>
               <li>Справка о доходах (для суммы свыше 5 млн ₸)</li>
               <li>Трудовая книжка или справка с места работы</li>
               <li>Документы на залоговое имущество (при наличии)</li>
             </ul>
             <p class="mt-3"><strong>Упрощенное оформление:</strong> до 5 млн ₸ без справок о доходах!</p>`
  },
  {
    question: 'Сколько времени занимает рассмотрение заявки?',
    answer: `<p>Быстрое рассмотрение на всех этапах:</p>
             <ul class="list-disc ml-6 mt-2 space-y-1">
               <li><strong>Предварительное одобрение:</strong> 15-60 минут</li>
               <li><strong>Окончательное решение:</strong> 1-3 часа</li>
               <li><strong>Подготовка документов:</strong> 1 день</li>
               <li><strong>Получение автомобиля:</strong> 1-2 дня</li>
             </ul>
             <p class="mt-3">В большинстве случаев весь процесс от заявки до получения ключей занимает 2-3 дня.</p>`
  },
  {
    question: 'Какова минимальная и максимальная сумма кредита?',
    answer: `<p>Гибкие условия кредитования:</p>
             <ul class="list-disc ml-6 mt-2 space-y-1">
               <li><strong>Минимальная сумма:</strong> 3 000 000 ₸</li>
               <li><strong>Максимальная сумма:</strong> 30 000 000 ₸</li>
               <li><strong>Срок кредита:</strong> от 1 года до 7 лет</li>
               <li><strong>Первый взнос:</strong> от 0% до 90%</li>
             </ul>
             <p class="mt-3">Точная сумма зависит от вашего дохода, кредитной истории и выбранного автомобиля.</p>`
  },
  {
    question: 'Что делать, если плохая кредитная история?',
    answer: `<p>Мы работаем с разными категориями заемщиков:</p>
             <ul class="list-disc ml-6 mt-2 space-y-1">
               <li>Индивидуальный подход к каждому клиенту</li>
               <li>Возможность оформления с поручителем</li>
               <li>Повышенный первоначальный взнос (от 30%)</li>
               <li>Рассмотрение альтернативных программ</li>
             </ul>
             <p class="mt-3"><strong>Совет:</strong> честно укажите все данные в заявке - это повышает шансы на одобрение.</p>`
  }
];

function renderFAQ() {
  const faqList = document.getElementById('faq-list');
  faqList.innerHTML = faqData.map((faq, index) => `
    <div class="card overflow-hidden">
      <button onclick="toggleFaq(${index})" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors duration-200">
        <h3 class="text-lg font-semibold text-gray-900 pr-4">${faq.question}</h3>
        <i id="faq-icon-${index}" class="fas fa-plus transition-transform duration-200 text-blue-600"></i>
      </button>
      <div id="faq-answer-${index}" class="hidden px-6 pb-6 border-t border-gray-100">
        <div class="pt-4 text-gray-700 leading-relaxed">${faq.answer}</div>
      </div>
    </div>
  `).join('');
  
  // Open first FAQ by default
  toggleFaq(0);
}

function toggleFaq(index) {
  const answer = document.getElementById(`faq-answer-${index}`);
  const icon = document.getElementById(`faq-icon-${index}`);
  
  if (answer.classList.contains('hidden')) {
    answer.classList.remove('hidden');
    icon.className = 'fas fa-minus transition-transform duration-200 text-blue-600';
  } else {
    answer.classList.add('hidden');
    icon.className = 'fas fa-plus transition-transform duration-200 text-blue-600';
  }
}

// Countdown timer
function startCountdown() {
  let days = 3;
  let hours = 23;
  let minutes = 59;
  let seconds = 59;
  
  function updateTimer() {
    if (seconds > 0) {
      seconds--;
    } else if (minutes > 0) {
      minutes--;
      seconds = 59;
    } else if (hours > 0) {
      hours--;
      minutes = 59;
      seconds = 59;
    } else if (days > 0) {
      days--;
      hours = 23;
      minutes = 59;
      seconds = 59;
    }
    
    document.getElementById('days').textContent = days.toString().padStart(2, '0');
    document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
    document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
    document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
  }
  
  countdownInterval = setInterval(updateTimer, 1000);
}

// Form submission
function handleFormSubmission(formElement, source = 'unknown') {
  const formData = new FormData(formElement);
  const data = Object.fromEntries(formData.entries());
  
  console.log('Form submitted:', { ...data, source });
  
  // Show success message
  alert('Заявка успешно отправлена! Мы свяжемся с вами в течение 15 минут.');
  
  // Reset form
  formElement.reset();
  
  // Hide modal if open
  hideApplicationModal();
  
  return false; // Prevent default form submission
}

function playVideo() {
  alert('Видео будет воспроизведено в отдельном окне');
}

// Update promo end date
function updatePromoEndDate() {
  const date = new Date();
  date.setDate(date.getDate() + 3);
  const formattedDate = date.toLocaleDateString('ru-RU', { 
    day: 'numeric', 
    month: 'long' 
  });
  document.getElementById('promo-end-date').textContent = formattedDate;
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  // Initialize components
  renderCars();
  renderFAQ();
  updateCalculations();
  startCountdown();
  updatePromoEndDate();
  
  // Start hero slider auto-play
  setInterval(nextSlide, 5000);
  
  // Setup form event listeners
  document.getElementById('application-form').addEventListener('submit', function(e) {
    e.preventDefault();
    handleFormSubmission(this, 'calculator_modal');
  });
  
  document.getElementById('final-form').addEventListener('submit', function(e) {
    e.preventDefault();
    handleFormSubmission(this, 'final_cta');
  });
  
  // Close modal when clicking outside
  document.getElementById('application-modal').addEventListener('click', function(e) {
    if (e.target === this) {
      hideApplicationModal();
    }
  });
});

// Handle page unload
window.addEventListener('beforeunload', function() {
  if (countdownInterval) {
    clearInterval(countdownInterval);
  }
});

// ==================== CATALOG PAGE ====================
let currentSort = 'price-asc';
let displayedCarsCount = 0;
const initialItemsPerPage = 28; // Initial load shows 28 cars
const loadMoreItemsCount = 3; // Load 3 more cars each time
let filteredCars = [];
let isLoadingMore = false;

function initCatalogPage() {
    console.log('Initializing catalog page...');
    
    // Initialize filter counts
    updateFilterCounts();
    
    // Set up event listeners
    setupCatalogEventListeners();
    
    // Initial load
    applyFiltersAndSort();
    
    // Simulate initial loading
    const loadingSpinner = document.getElementById('loading-spinner');
    if (loadingSpinner) {
        loadingSpinner.classList.remove('hidden');
        setTimeout(() => {
            loadingSpinner.classList.add('hidden');
            displayCars();
        }, 1000);
    } else {
        displayCars();
    }
}

function setupCatalogEventListeners() {
    // Filter tabs
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const filter = this.dataset.filter;
            setActiveFilter(filter);
        });
    });
    
    // Sort dropdown
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            currentSort = this.value;
            applyFiltersAndSort();
            displayCars();
        });
    }
    
    // Load more button
    const loadMoreBtn = document.getElementById('load-more-btn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', loadMoreCars);
    }
}

function updateFilterCounts() {
    const filters = {
        'all': cars.length,
        'electric': cars.filter(car => car.type === 'electric').length,
        'hybrid': cars.filter(car => car.type === 'hybrid').length,
        'crossover': cars.filter(car => car.type === 'crossover').length,
        'sedan': cars.filter(car => car.type === 'sedan').length,
        'pickup': cars.filter(car => car.type === 'pickup').length
    };
    
    document.querySelectorAll('.filter-tab').forEach(tab => {
        const filter = tab.dataset.filter;
        const countSpan = tab.querySelector('.count');
        if (countSpan && filters[filter] !== undefined) {
            countSpan.textContent = filters[filter];
        }
    });
    
    // Update total cars counter
    const totalCarsElement = document.getElementById('total-cars-count');
    const carsCounterElement = document.getElementById('cars-counter');
    if (totalCarsElement) totalCarsElement.textContent = cars.length;
    if (carsCounterElement) carsCounterElement.textContent = `${cars.length} автомобилей`;
}

function setActiveFilter(filter) {
    currentFilter = filter;
    displayedCarsCount = 0;
    
    // Update active state
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelector(`[data-filter="${filter}"]`).classList.add('active');
    
    applyFiltersAndSort();
    displayCars();
}

function applyFiltersAndSort() {
    // Apply filters
    if (currentFilter === 'all') {
        filteredCars = [...cars];
    } else {
        filteredCars = cars.filter(car => car.type === currentFilter);
    }
    
    // Apply sorting
    switch (currentSort) {
        case 'price-asc':
            filteredCars.sort((a, b) => a.price - b.price);
            break;
        case 'price-desc':
            filteredCars.sort((a, b) => b.price - a.price);
            break;
        case 'payment-asc':
            filteredCars.sort((a, b) => a.monthlyPayment - b.monthlyPayment);
            break;
        case 'payment-desc':
            filteredCars.sort((a, b) => b.monthlyPayment - a.monthlyPayment);
            break;
        case 'name':
            filteredCars.sort((a, b) => a.name.localeCompare(b.name));
            break;
    }
    
    // Reset displayed count
    displayedCarsCount = 0;
}

function displayCars() {
    const carsGrid = document.getElementById('cars-grid');
    if (!carsGrid) return;
    
    // Update results info
    updateResultsInfo();
    
    // Show/hide states
    if (filteredCars.length === 0) {
        showEmptyState();
        return;
    } else {
        hideEmptyState();
    }
    
    // Calculate cars to show
    const newDisplayCount = Math.min(displayedCarsCount + (displayedCarsCount === 0 ? initialItemsPerPage : loadMoreItemsCount), filteredCars.length);
    const carsToShow = filteredCars.slice(0, newDisplayCount);
    
    // Clear grid if starting fresh
    if (displayedCarsCount === 0) {
        carsGrid.innerHTML = '';
    }
    
    // Add new cars
    const newCars = filteredCars.slice(displayedCarsCount, newDisplayCount);
    newCars.forEach(car => {
        const carElement = createCarCard(car);
        carsGrid.appendChild(carElement);
    });
    
    displayedCarsCount = newDisplayCount;
    
    // Update load more button
    updateLoadMoreButton();
}

function createCarCard(car) {
    const div = document.createElement('div');
    div.className = 'card cursor-pointer group overflow-hidden';
    div.onclick = () => window.location.href = `car-details.html?id=${car.id}`;
    
    div.innerHTML = `
        <div class="relative overflow-hidden">
            <img src="${car.image}" alt="${car.name}" 
                 class="w-full h-48 object-cover group-hover:scale-110 transition-transform duration-500" 
                 loading="lazy">
            <div class="absolute top-4 left-4">
                <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                    ${getCarTypeLabel(car.type)}
                </span>
            </div>
            <div class="absolute top-4 right-4">
                <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                    АКЦИЯ
                </span>
            </div>
        </div>
        
        <div class="p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                ${car.name}
            </h3>
            
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Двигатель:</span>
                    <span class="font-semibold">${car.specifications?.engine || 'Не указан'}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Мощность:</span>
                    <span class="font-semibold">${car.specifications?.power || 'Не указана'}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Привод:</span>
                    <span class="font-semibold">${car.specifications?.drive || 'Не указан'}</span>
                </div>
            </div>
            
            <div class="border-t pt-4">
                <div class="text-2xl font-bold text-gray-900 mb-2">
                    ${formatPrice(car.price)} ₸
                </div>
                <div class="text-lg font-semibold text-green-600">
                    от ${formatPrice(car.monthlyPayment)} ₸/мес
                </div>
                <div class="text-sm text-gray-500 mt-1">
                    от ${car.loanOptions?.interestRate || '5.9'}% годовых
                </div>
            </div>
            
            <button class="block w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors duration-200 text-center">
                Подробнее
            </button>
        </div>
    `;
    
    return div;
}

function updateResultsInfo() {
    const resultsCount = document.getElementById('results-count');
    const resultsText = document.getElementById('results-text');
    
    if (resultsCount) resultsCount.textContent = filteredCars.length;
    if (resultsText) resultsText.textContent = getResultText(filteredCars.length);
}

function getResultText(count) {
    if (count % 10 === 1 && count % 100 !== 11) return 'автомобиль';
    if ([2, 3, 4].includes(count % 10) && ![12, 13, 14].includes(count % 100)) return 'автомобиля';
    return 'автомобилей';
}

function updateLoadMoreButton() {
    const loadMoreContainer = document.getElementById('load-more-container');
    const endMessage = document.getElementById('end-message');
    const loadMoreBtn = document.getElementById('load-more-btn');
    const countSpan = loadMoreBtn?.querySelector('.count');
    
    if (displayedCarsCount < filteredCars.length) {
        // Show load more button
        if (loadMoreContainer) loadMoreContainer.classList.remove('hidden');
        if (endMessage) endMessage.classList.add('hidden');
        if (countSpan) countSpan.textContent = `(${filteredCars.length - displayedCarsCount})`;
    } else if (displayedCarsCount > initialItemsPerPage) {
        // Show end message only if we loaded more than initial page
        if (loadMoreContainer) loadMoreContainer.classList.add('hidden');
        if (endMessage) {
            endMessage.classList.remove('hidden');
            const displayedCountSpan = endMessage.querySelector('#displayed-count');
            const totalCountSpan = endMessage.querySelector('#total-count');
            if (displayedCountSpan) displayedCountSpan.textContent = displayedCarsCount;
            if (totalCountSpan) totalCountSpan.textContent = filteredCars.length;
        }
    } else {
        // Hide both
        if (loadMoreContainer) loadMoreContainer.classList.add('hidden');
        if (endMessage) endMessage.classList.add('hidden');
    }
}

function loadMoreCars() {
    if (isLoadingMore) return;
    
    isLoadingMore = true;
    const loadMoreBtn = document.getElementById('load-more-btn');
    const textSpan = loadMoreBtn?.querySelector('.text');
    
    if (textSpan) textSpan.textContent = 'Загружаем...';
    if (loadMoreBtn) loadMoreBtn.disabled = true;
    
    // Simulate loading delay
    setTimeout(() => {
        displayCars();
        isLoadingMore = false;
        if (textSpan) textSpan.textContent = 'Показать ещё';
        if (loadMoreBtn) loadMoreBtn.disabled = false;
    }, 500);
}

function showEmptyState() {
    const emptyState = document.getElementById('empty-state');
    if (emptyState) emptyState.classList.remove('hidden');
}

function hideEmptyState() {
    const emptyState = document.getElementById('empty-state');
    if (emptyState) emptyState.classList.add('hidden');
}

function getCarTypeLabel(type) {
    const labels = {
        'electric': 'Электромобиль',
        'hybrid': 'Гибрид',
        'crossover': 'Кроссовер',
        'sedan': 'Седан',
        'pickup': 'Пикап'
    };
    return labels[type] || type;
}

// ==================== CAR DETAILS PAGE ====================
let currentCar = null;
let selectedImage = '';
let selectedColor = null;

function initCarDetailsPage() {
    console.log('Initializing car details page...');
    
    // Get car ID from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const carId = urlParams.get('id') || urlParams.get('car');
    
    if (!carId) {
        showNotFound();
        return;
    }
    
    // Find car by ID
    currentCar = cars.find(car => car.id === carId);
    
    if (!currentCar) {
        showNotFound();
        return;
    }
    
    // Load car details
    loadCarDetails();
}

function showNotFound() {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('not-found-state').classList.remove('hidden');
}

function loadCarDetails() {
    // Hide loading state
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('car-details').classList.remove('hidden');
    
    // Update page title and meta
    document.title = `${currentCar.name} - Купить в кредит - Tachki.kz`;
    document.getElementById('page-title').textContent = document.title;
    document.getElementById('page-description').content = 
        `${currentCar.name} в кредит от ${formatPrice(currentCar.monthlyPayment)} тенге в месяц. ${currentCar.description}`;
    
    // Load basic info
    loadBasicInfo();
    loadImageGallery();
    loadColorOptions();
    loadPricing();
    loadQuickSpecs();
    loadSpecifications();
    loadFeatures();
    loadAdvantages();
    loadAdvantagePhotos();
}

function loadBasicInfo() {
    document.getElementById('breadcrumb-car-name').textContent = currentCar.name;
    document.getElementById('car-name').textContent = currentCar.name;
    document.getElementById('car-description').textContent = currentCar.description;
    document.getElementById('car-type-badge').textContent = getCarTypeLabel(currentCar.type);
}

function loadImageGallery() {
    const selectedImageEl = document.getElementById('selected-image');
    const thumbnailsContainer = document.getElementById('image-thumbnails');
    
    // Set initial image
    selectedImage = currentCar.images?.[0] || currentCar.image;
    selectedImageEl.src = selectedImage;
    selectedImageEl.alt = currentCar.name;
    
    // Create thumbnails
    if (currentCar.images) {
        thumbnailsContainer.innerHTML = '';
        currentCar.images.forEach((image, index) => {
            const thumbnail = document.createElement('div');
            thumbnail.className = `aspect-video bg-gray-200 rounded cursor-pointer overflow-hidden border-2 ${
                image === selectedImage ? 'border-blue-500' : 'border-transparent hover:border-gray-400'
            }`;
            thumbnail.onclick = () => selectImage(image);
            
            const img = document.createElement('img');
            img.src = image;
            img.alt = `${currentCar.name} ${index + 1}`;
            img.className = 'w-full h-full object-cover';
            
            thumbnail.appendChild(img);
            thumbnailsContainer.appendChild(thumbnail);
        });
    }
}

function selectImage(image) {
    selectedImage = image;
    document.getElementById('selected-image').src = image;
    
    // Update thumbnail borders
    document.querySelectorAll('#image-thumbnails > div').forEach(thumb => {
        thumb.className = thumb.className.replace('border-blue-500', 'border-transparent');
    });
    
    const selectedThumb = Array.from(document.querySelectorAll('#image-thumbnails img'))
        .find(img => img.src === image)?.parentElement;
    if (selectedThumb) {
        selectedThumb.className = selectedThumb.className.replace('border-transparent', 'border-blue-500');
    }
}

function loadColorOptions() {
    const colorOptionsContainer = document.getElementById('color-options');
    
    if (!currentCar.colors || currentCar.colors.length === 0) {
        document.getElementById('color-selection-container').style.display = 'none';
        return;
    }
    
    selectedColor = currentCar.colors[0];
    colorOptionsContainer.innerHTML = '';
    
    currentCar.colors.forEach(color => {
        const colorOption = document.createElement('div');
        colorOption.className = 'cursor-pointer group relative';
        colorOption.title = color.name;
        colorOption.onclick = () => selectColor(color);
        
        const colorDiv = document.createElement('div');
        colorDiv.className = `w-10 h-10 rounded-full border-4 transition-all duration-200 ${
            selectedColor?.name === color.name 
                ? 'border-blue-500 shadow-lg scale-110' 
                : 'border-gray-300 hover:border-gray-400 hover:scale-105'
        }`;
        colorDiv.style.backgroundColor = color.hex;
        
        colorOption.appendChild(colorDiv);
        
        if (selectedColor?.name === color.name) {
            const indicator = document.createElement('div');
            indicator.className = 'absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-blue-500 rounded-full';
            colorOption.appendChild(indicator);
        }
        
        colorOptionsContainer.appendChild(colorOption);
    });
}

function selectColor(color) {
    selectedColor = color;
    if (color.image) {
        selectImage(color.image);
    }
    loadColorOptions(); // Refresh to update selected state
}

function loadPricing() {
    document.getElementById('car-price').textContent = `${formatPrice(currentCar.price)} ₸`;
    document.getElementById('monthly-payment').textContent = `${formatPrice(currentCar.monthlyPayment)} ₸/мес`;
    
    const loanOptions = document.getElementById('loan-options');
    if (currentCar.loanOptions) {
        loanOptions.innerHTML = `
            <div>
                <strong>Первый взнос:</strong> от ${currentCar.loanOptions.minDownPayment}%
            </div>
            <div>
                <strong>Срок:</strong> до ${currentCar.loanOptions.maxTerm} мес
            </div>
            <div>
                <strong>Ставка:</strong> от ${currentCar.loanOptions.interestRate}%
            </div>
        `;
    }
}

function loadQuickSpecs() {
    const quickSpecsContainer = document.getElementById('quick-specs');
    const specs = currentCar.specifications;
    
    if (!specs) return;
    
    const specItems = [
        { icon: 'fas fa-cogs', color: 'blue-600', label: 'Двигатель', value: specs.engine },
        { icon: 'fas fa-tachometer-alt', color: 'green-600', label: 'Мощность', value: specs.power },
        { icon: 'fas fa-gas-pump', color: 'orange-600', label: 'Расход', value: specs.fuelConsumption },
        { icon: 'fas fa-road', color: 'purple-600', label: 'Привод', value: specs.drive }
    ];
    
    quickSpecsContainer.innerHTML = '';
    specItems.forEach(item => {
        if (item.value) {
            const specCard = document.createElement('div');
            specCard.className = 'flex items-center space-x-3 p-4 bg-white rounded-lg shadow-sm';
            specCard.innerHTML = `
                <i class="${item.icon} text-${item.color} text-xl"></i>
                <div>
                    <div class="text-sm text-gray-600">${item.label}</div>
                    <div class="font-semibold">${item.value}</div>
                </div>
            `;
            quickSpecsContainer.appendChild(specCard);
        }
    });
}

function loadSpecifications() {
    const specificationsContainer = document.getElementById('specifications');
    const specs = currentCar.specifications;
    
    if (!specs) return;
    
    const leftColumn = document.createElement('div');
    leftColumn.className = 'space-y-4';
    
    const rightColumn = document.createElement('div');
    rightColumn.className = 'space-y-4';
    
    const leftSpecs = [
        { label: 'Двигатель', value: specs.engine },
        { label: 'Мощность', value: specs.power },
        { label: 'Расход топлива', value: specs.fuelConsumption },
        { label: 'Коробка передач', value: specs.transmission }
    ];
    
    const rightSpecs = [
        { label: 'Привод', value: specs.drive },
        { label: 'Разгон 0-100 км/ч', value: specs.acceleration },
        { label: 'Максимальная скорость', value: specs.maxSpeed },
        { label: 'Емкость батареи', value: specs.batteryCapacity },
        { label: 'Запас хода', value: specs.electricRange }
    ];
    
    leftSpecs.forEach(spec => {
        if (spec.value) {
            const specRow = document.createElement('div');
            specRow.className = 'flex justify-between py-3 border-b border-gray-200';
            specRow.innerHTML = `
                <span class="text-gray-600">${spec.label}</span>
                <span class="font-semibold">${spec.value}</span>
            `;
            leftColumn.appendChild(specRow);
        }
    });
    
    rightSpecs.forEach(spec => {
        if (spec.value) {
            const specRow = document.createElement('div');
            specRow.className = 'flex justify-between py-3 border-b border-gray-200';
            specRow.innerHTML = `
                <span class="text-gray-600">${spec.label}</span>
                <span class="font-semibold">${spec.value}</span>
            `;
            rightColumn.appendChild(specRow);
        }
    });
    
    specificationsContainer.innerHTML = '';
    specificationsContainer.appendChild(leftColumn);
    specificationsContainer.appendChild(rightColumn);
}

function loadFeatures() {
    const featuresContainer = document.getElementById('features');
    
    if (!currentCar.features || currentCar.features.length === 0) {
        featuresContainer.parentElement.style.display = 'none';
        return;
    }
    
    featuresContainer.innerHTML = '';
    currentCar.features.forEach(feature => {
        const featureItem = document.createElement('div');
        featureItem.className = 'flex items-center space-x-3 p-3 bg-gray-50 rounded-lg';
        featureItem.innerHTML = `
            <i class="fas fa-check text-green-500"></i>
            <span>${feature}</span>
        `;
        featuresContainer.appendChild(featureItem);
    });
}

function loadAdvantages() {
    const advantagesContainer = document.getElementById('advantages');
    
    if (!currentCar.advantages || currentCar.advantages.length === 0) {
        advantagesContainer.parentElement.style.display = 'none';
        return;
    }
    
    advantagesContainer.innerHTML = '';
    currentCar.advantages.forEach((advantage, index) => {
        const advantageItem = document.createElement('div');
        advantageItem.className = 'flex items-start space-x-4 p-4 bg-blue-50 rounded-lg';
        advantageItem.innerHTML = `
            <div class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold flex-shrink-0">
                ${index + 1}
            </div>
            <span class="text-gray-700">${advantage}</span>
        `;
        advantagesContainer.appendChild(advantageItem);
    });
}

function loadAdvantagePhotos() {
    const advantagePhotosContainer = document.getElementById('advantages-gallery');
    
    if (!currentCar) return;
    
    // Default advantage photos for each car type
    const defaultAdvantagePhotos = [
        {
            image: 'https://images.unsplash.com/photo-1550355291-bbee04a92027?w=400&h=300&fit=crop',
            title: 'Экономичность',
            description: 'Низкий расход топлива и минимальные затраты на обслуживание'
        },
        {
            image: 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=400&h=300&fit=crop',
            title: 'Безопасность',
            description: 'Высокие рейтинги безопасности и современные системы защиты'
        },
        {
            image: 'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=400&h=300&fit=crop',
            title: 'Комфорт',
            description: 'Просторный салон и удобная эргономика для любых поездок'
        },
        {
            image: 'https://images.unsplash.com/photo-1494976154464-a7f76aa14a6d?w=400&h=300&fit=crop',
            title: 'Технологии',
            description: 'Современные мультимедийные системы и умные функции'
        },
        {
            image: 'https://images.unsplash.com/photo-1609521263047-f8f205293f24?w=400&h=300&fit=crop',
            title: 'Надежность',
            description: 'Проверенное качество и долговечность китайских автомобилей'
        },
        {
            image: 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=400&h=300&fit=crop',
            title: 'Стиль',
            description: 'Современный дизайн и привлекательный внешний вид'
        }
    ];
    
    // Use car-specific advantage photos if available, otherwise use defaults
    const advantagePhotos = currentCar.advantagePhotos || defaultAdvantagePhotos;
    
    // Populate photos grid
    advantagePhotosContainer.innerHTML = '';
    advantagePhotos.forEach(photo => {
        const photoCard = document.createElement('div');
        photoCard.className = 'group cursor-pointer';
        photoCard.innerHTML = `
            <div class="relative overflow-hidden rounded-xl shadow-lg">
                <img src="${photo.image}" alt="${photo.title}" 
                     class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-black bg-opacity-40 group-hover:bg-opacity-60 transition-all duration-300"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <h3 class="text-xl font-bold mb-2">${photo.title}</h3>
                    <p class="text-sm opacity-90">${photo.description}</p>
                </div>
            </div>
        `;
        advantagePhotosContainer.appendChild(photoCard);
    });
    
    // Populate key benefits
    const keyBenefits = currentCar.keyBenefits || [
        {
            icon: 'fas fa-leaf',
            color: 'green-600',
            title: 'Экологичность',
            description: 'Современные двигатели с низким уровнем выбросов'
        },
        {
            icon: 'fas fa-shield-alt',
            color: 'blue-600', 
            title: 'Безопасность',
            description: '5-звездочный рейтинг безопасности Euro NCAP'
        },
        {
            icon: 'fas fa-dollar-sign',
            color: 'orange-600',
            title: 'Выгодная цена',
            description: 'Лучшее соотношение цена-качество на рынке'
        },
        {
            icon: 'fas fa-wrench',
            color: 'purple-600',
            title: 'Надежный сервис',
            description: 'Официальная гарантия и сервисная поддержка'
        }
    ];
    
    keyBenefitsContainer.innerHTML = '';
    keyBenefits.forEach(benefit => {
        const benefitItem = document.createElement('div');
        benefitItem.className = 'flex items-start space-x-4';
        benefitItem.innerHTML = `
            <div class="bg-${benefit.color} bg-opacity-10 rounded-lg p-3 flex-shrink-0">
                <i class="${benefit.icon} text-${benefit.color} text-xl"></i>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 mb-1">${benefit.title}</h4>
                <p class="text-gray-600 text-sm">${benefit.description}</p>
            </div>
        `;
        keyBenefitsContainer.appendChild(benefitItem);
    });
}

function requestTestDrive() {
    alert('Функция записи на тест-драйв будет доступна в ближайшее время!');
}

function requestCallback() {
    const phone = prompt('Введите ваш номер телефона для обратного звонка:');
    if (phone) {
        alert('Спасибо! Мы перезвоним вам в ближайшее время.');
    }
}

// Override showLoanModal for car details page to show selected car
function showLoanModalWithCar() {
    if (currentCar) {
        // Update modal with car info
        const modalCarImage = document.getElementById('modal-car-image');
        const modalCarName = document.getElementById('modal-car-name');
        const modalCarPayment = document.getElementById('modal-car-payment');
        const selectedCarInfo = document.getElementById('selected-car-info');
        
        if (modalCarImage) modalCarImage.src = currentCar.image;
        if (modalCarName) modalCarName.textContent = currentCar.name;
        if (modalCarPayment) modalCarPayment.textContent = `от ${formatPrice(currentCar.monthlyPayment)} ₸/мес`;
        if (selectedCarInfo) selectedCarInfo.classList.remove('hidden');
    }
    
    showLoanModal();
}

// Export functions for global use
window.toggleMobileMenu = toggleMobileMenu;
window.formatPrice = formatPrice;
window.submitLoanApplication = submitLoanApplication;

// Catalog page functions
window.initCatalogPage = initCatalogPage;
window.setActiveFilter = setActiveFilter;

// Car details page functions  
window.initCarDetailsPage = initCarDetailsPage;
window.requestTestDrive = requestTestDrive;
window.requestCallback = requestCallback;
window.showLoanModalWithCar = showLoanModalWithCar;