// Car data converted from TypeScript
const cars = [
  {
    id: 'byd-song-plus',
    name: 'BYD Song Plus',
    brand: 'BYD',
    model: 'Song Plus',
    type: 'electric',
    price: 12500000,
    monthlyPayment: 156000,
    image: 'https://images.unsplash.com/photo-1617886322207-baac93ab248c?w=800&h=500&fit=crop&crop=center',
    images: [
      'https://images.unsplash.com/photo-1617886322207-baac93ab248c?w=800&h=500&fit=crop&crop=center',
      'https://images.unsplash.com/photo-1616584273943-6bd31efd2b57?w=800&h=500&fit=crop&crop=center',
      'https://images.unsplash.com/photo-1605559911160-a3d95d213904?w=800&h=500&fit=crop&crop=center',
      'https://images.unsplash.com/photo-1621135802920-133df287f89c?w=800&h=500&fit=crop&crop=center'
    ],
    description: 'Электрический кроссовер BYD Song Plus - это идеальное сочетание современных технологий, комфорта и экологичности.',
    specifications: {
      engine: 'Электродвигатель',
      power: '184 л.с.',
      fuelConsumption: '0 л/100км',
      transmission: 'Автоматическая',
      drive: 'Передний',
      acceleration: '8.5 сек (0-100 км/ч)',
      maxSpeed: '120 км/ч',
      batteryCapacity: '71.7 кВт⋅ч',
      electricRange: '505 км'
    },
    features: [
      'Быстрая зарядка DC до 80% за 30 мин',
      'Панорамная крыша',
      '12.8" поворотный сенсорный экран',
      'Система помощи водителю DiPilot',
      'Климат-контроль с очисткой воздуха',
      'Кожаная отделка салона',
      'LED оптика',
      'Беспроводная зарядка смартфона'
    ],
    advantages: [
      'Нулевые выбросы CO2',
      'Экономия на топливе до 300,000 тенге в год',
      'Тихий ход двигателя',
      'Мгновенный отклик на педаль газа',
      'Низкие расходы на обслуживание',
      'Государственные льготы для электромобилей'
    ],
    colors: [
      { name: 'Белый перламутр', hex: '#FFFFFF', image: 'https://images.unsplash.com/photo-1617886322207-baac93ab248c?w=400&h=300&fit=crop&crop=center' },
      { name: 'Серый металлик', hex: '#6B7280', image: 'https://images.unsplash.com/photo-1616584273943-6bd31efd2b57?w=400&h=300&fit=crop&crop=center' },
      { name: 'Черный', hex: '#111827', image: 'https://images.unsplash.com/photo-1605559911160-a3d95d213904?w=400&h=300&fit=crop&crop=center' }
    ],
    loanOptions: {
      minDownPayment: 0,
      maxTerm: 84,
      interestRate: 5.9
    }
  },
  {
    id: 'chery-tiggo-8-pro',
    name: 'Chery Tiggo 8 Pro',
    brand: 'Chery',
    model: 'Tiggo 8 Pro',
    type: 'crossover',
    price: 8900000,
    monthlyPayment: 111000,
    image: 'https://images.unsplash.com/photo-1590362891991-f776e747a588?w=800&h=500&fit=crop&crop=center',
    images: [
      'https://images.unsplash.com/photo-1590362891991-f776e747a588?w=800&h=500&fit=crop&crop=center',
      'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=500&fit=crop&crop=center',
      'https://images.unsplash.com/photo-1505073692733-1e4e04e9acd8?w=800&h=500&fit=crop&crop=center',
      'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=500&fit=crop&crop=center'
    ],
    description: 'Chery Tiggo 8 Pro - семейный кроссовер премиум-класса с просторным салоном на 7 мест.',
    specifications: {
      engine: '1.6 л турбо',
      power: '197 л.с.',
      fuelConsumption: '7.4 л/100км',
      transmission: '7DCT робот',
      drive: 'Полный AWD',
      acceleration: '9.6 сек (0-100 км/ч)',
      maxSpeed: '180 км/ч'
    },
    features: [
      '7-местный салон с раскладными сиденьями',
      'Панорамная крыша с люком',
      '13.2" мультимедийная система',
      'Система кругового обзора 360°',
      'Адаптивный круиз-контроль',
      'Электропривод багажника',
      'Подогрев и вентиляция передних сидений',
      'Система контроля слепых зон'
    ],
    advantages: [
      'Просторный 7-местный салон',
      'Полный привод для любых дорог',
      'Современная система безопасности',
      'Экономичный расход топлива',
      'Высокий клиренс 190 мм',
      'Официальная гарантия 7 лет'
    ],
    colors: [
      { name: 'Белый', hex: '#FFFFFF', image: 'https://images.unsplash.com/photo-1590362891991-f776e747a588?w=400&h=300&fit=crop&crop=center' },
      { name: 'Серый', hex: '#6B7280', image: 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=400&h=300&fit=crop&crop=center' },
      { name: 'Синий', hex: '#1E40AF', image: 'https://images.unsplash.com/photo-1505073692733-1e4e04e9acd8?w=400&h=300&fit=crop&crop=center' }
    ],
    loanOptions: {
      minDownPayment: 10,
      maxTerm: 84,
      interestRate: 5.9
    }
  },
  {
    id: 'hongqi-h5',
    name: 'Hongqi H5',
    brand: 'Hongqi',
    model: 'H5',
    type: 'sedan',
    price: 7200000,
    monthlyPayment: 90000,
    image: 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=500&fit=crop&crop=center',
    images: [
      'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=500&fit=crop&crop=center',
      'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=500&fit=crop&crop=center',
      'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=800&h=500&fit=crop&crop=center',
      'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=500&fit=crop&crop=center'
    ],
    description: 'Hongqi H5 - премиальный седан бизнес-класса с изысканным дизайном и богатым оснащением.',
    specifications: {
      engine: '1.8 л турбо',
      power: '178 л.с.',
      fuelConsumption: '6.8 л/100км',
      transmission: '6AT автомат',
      drive: 'Передний',
      acceleration: '9.8 сек (0-100 км/ч)',
      maxSpeed: '200 км/ч'
    },
    features: [
      'Кожаный салон с декором под дерево',
      '12.3" приборная панель',
      '10.25" мультимедиа система',
      'Панорамная крыша',
      'Система климат-контроль',
      'Подогрев передних и задних сидений',
      'Электрорегулировка сидений',
      'Премиум аудиосистема'
    ],
    advantages: [
      'Представительский внешний вид',
      'Комфортабельный салон',
      'Экономичный двигатель',
      'Богатое базовое оснащение',
      'Плавный ход подвески',
      'Престижный бренд'
    ],
    colors: [
      { name: 'Черный металлик', hex: '#111827', image: 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=400&h=300&fit=crop&crop=center' },
      { name: 'Серый', hex: '#6B7280', image: 'https://images.unsplash.com/photo-1563720223185-11003d516935?w=400&h=300&fit=crop&crop=center' },
      { name: 'Белый перламутр', hex: '#FFFFFF', image: 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=400&h=300&fit=crop&crop=center' }
    ],
    loanOptions: {
      minDownPayment: 15,
      maxTerm: 60,
      interestRate: 6.9
    }
  },
  {
    id: 'jac-t8',
    name: 'JAC T8',
    brand: 'JAC',
    model: 'T8',
    type: 'pickup',
    price: 9800000,
    monthlyPayment: 122000,
    image: 'https://images.unsplash.com/photo-1565043589221-1a6fd9ae45c7?w=800&h=500&fit=crop&crop=center',
    images: [
      'https://images.unsplash.com/photo-1565043589221-1a6fd9ae45c7?w=800&h=500&fit=crop&crop=center',
      'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=500&fit=crop&crop=center',
      'https://images.unsplash.com/photo-1520031441872-265e4ff70366?w=800&h=500&fit=crop&crop=center',
      'https://images.unsplash.com/photo-1502877338535-766e1452684a?w=800&h=500&fit=crop&crop=center'
    ],
    description: 'JAC T8 - мощный пикап с высокими рабочими характеристиками.',
    specifications: {
      engine: '2.0 л турбо дизель',
      power: '160 л.с.',
      fuelConsumption: '8.5 л/100км',
      transmission: '6MT механика',
      drive: 'Полный 4WD',
      acceleration: '12.5 сек (0-100 км/ч)',
      maxSpeed: '165 км/ч'
    },
    features: [
      'Грузоподъемность до 1 тонны',
      'Система блокировки дифференциала',
      '9" мультимедиа система',
      'Кожаные сиденья',
      'Система контроля спуска с горы',
      'Задняя камера',
      'Круиз-контроль',
      'Защитная дуга в кузове'
    ],
    advantages: [
      'Высокая грузоподъемность',
      'Отличная проходимость',
      'Надежный дизельный двигатель',
      'Комфортабельная кабина',
      'Система полного привода',
      'Подходит для коммерческого использования'
    ],
    colors: [
      { name: 'Белый', hex: '#FFFFFF', image: 'https://images.unsplash.com/photo-1565043589221-1a6fd9ae45c7?w=400&h=300&fit=crop&crop=center' },
      { name: 'Серебристый', hex: '#9CA3AF', image: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=300&fit=crop&crop=center' },
      { name: 'Красный', hex: '#DC2626', image: 'https://images.unsplash.com/photo-1520031441872-265e4ff70366?w=400&h=300&fit=crop&crop=center' }
    ],
    loanOptions: {
      minDownPayment: 20,
      maxTerm: 60,
      interestRate: 7.9
    }
  },
  {
    id: 'byd-seal',
    name: 'BYD Seal',
    brand: 'BYD',
    model: 'Seal',
    type: 'sedan',
    price: 13200000,
    monthlyPayment: 165000,
    image: 'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?w=800&h=500&fit=crop&crop=center',
    description: 'BYD Seal - электрический седан премиум-класса с запасом хода до 650 км.',
    specifications: {
      engine: 'Электродвигатель',
      power: '230 л.с.',
      fuelConsumption: '0 л/100км',
      transmission: 'Автоматическая',
      drive: 'Задний',
      acceleration: '7.5 сек (0-100 км/ч)',
      maxSpeed: '180 км/ч',
      batteryCapacity: '82.5 кВт⋅ч',
      electricRange: '650 км'
    },
    loanOptions: {
      minDownPayment: 0,
      maxTerm: 84,
      interestRate: 5.9
    }
  },
  {
    id: 'geely-monjaro',
    name: 'Geely Monjaro',
    brand: 'Geely',
    model: 'Monjaro',
    type: 'crossover',
    price: 9500000,
    monthlyPayment: 118000,
    image: 'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?w=800&h=500&fit=crop&crop=center',
    description: 'Geely Monjaro - стильный кроссовер с передовыми технологиями безопасности.',
    specifications: {
      engine: '2.0 л турбо',
      power: '190 л.с.',
      fuelConsumption: '7.8 л/100км',
      transmission: '8AT автомат',
      drive: 'Полный AWD',
      acceleration: '9.2 сек (0-100 км/ч)',
      maxSpeed: '190 км/ч'
    },
    loanOptions: {
      minDownPayment: 10,
      maxTerm: 84,
      interestRate: 6.4
    }
  },
  {
    id: 'haval-jolion',
    name: 'Haval Jolion',
    brand: 'Haval',
    model: 'Jolion',
    type: 'crossover',
    price: 6800000,
    monthlyPayment: 85000,
    image: 'https://images.unsplash.com/photo-1601362840469-51e4d8d58785?w=800&h=500&fit=crop&crop=center',
    description: 'Haval Jolion - компактный городской кроссовер с отличным соотношением цена-качество.',
    specifications: {
      engine: '1.5 л турбо',
      power: '150 л.с.',
      fuelConsumption: '6.9 л/100км',
      transmission: '7DCT робот',
      drive: 'Передний',
      acceleration: '10.1 сек (0-100 км/ч)',
      maxSpeed: '175 км/ч'
    },
    loanOptions: {
      minDownPayment: 15,
      maxTerm: 60,
      interestRate: 6.9
    }
  },
  {
    id: 'changan-uni-t',
    name: 'Changan UNI-T',
    brand: 'Changan',
    model: 'UNI-T',
    type: 'crossover',
    price: 7500000,
    monthlyPayment: 94000,
    image: 'https://images.unsplash.com/photo-1605559424962-9e4c228ac24e?w=800&h=500&fit=crop&crop=center',
    description: 'Changan UNI-T - футуристический кроссовер с уникальным дизайном.',
    specifications: {
      engine: '1.5 л турбо',
      power: '180 л.с.',
      fuelConsumption: '7.2 л/100км',
      transmission: '7DCT робот',
      drive: 'Передний',
      acceleration: '9.5 сек (0-100 км/ч)',
      maxSpeed: '180 км/ч'
    },
    loanOptions: {
      minDownPayment: 15,
      maxTerm: 60,
      interestRate: 7.4
    }
  },
  {
    id: 'dongfeng-s50-ev',
    name: 'Dongfeng S50 EV',
    brand: 'Dongfeng',
    model: 'S50 EV',
    type: 'electric',
    price: 8200000,
    monthlyPayment: 102000,
    image: 'https://images.unsplash.com/photo-1621266876775-5c9d1f6d52e9?w=800&h=500&fit=crop&crop=center',
    description: 'Dongfeng S50 EV - доступный электрический седан для городских поездок.',
    specifications: {
      engine: 'Электродвигатель',
      power: '95 л.с.',
      fuelConsumption: '0 л/100км',
      transmission: 'Автоматическая',
      drive: 'Передний',
      acceleration: '12.5 сек (0-100 км/ч)',
      maxSpeed: '140 км/ч',
      batteryCapacity: '35.5 кВт⋅ч',
      electricRange: '305 км'
    },
    loanOptions: {
      minDownPayment: 0,
      maxTerm: 60,
      interestRate: 5.9
    }
  },
  {
    id: 'great-wall-poer',
    name: 'Great Wall Poer',
    brand: 'Great Wall',
    model: 'Poer',
    type: 'pickup',
    price: 11200000,
    monthlyPayment: 140000,
    image: 'https://images.unsplash.com/photo-1558618047-5c6c8d0e9162?w=800&h=500&fit=crop&crop=center',
    description: 'Great Wall Poer - современный пикап для работы и отдыха.',
    specifications: {
      engine: '2.0 л турбо',
      power: '190 л.с.',
      fuelConsumption: '9.2 л/100км',
      transmission: '8AT автомат',
      drive: 'Полный 4WD',
      acceleration: '10.5 сек (0-100 км/ч)',
      maxSpeed: '170 км/ч'
    },
    loanOptions: {
      minDownPayment: 20,
      maxTerm: 60,
      interestRate: 8.4
    }
  },
  {
    id: 'baic-x35',
    name: 'BAIC X35',
    brand: 'BAIC',
    model: 'X35',
    type: 'crossover',
    price: 5900000,
    monthlyPayment: 74000,
    image: 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=500&fit=crop&crop=center',
    description: 'BAIC X35 - бюджетный кроссовер с неплохим оснащением.',
    specifications: {
      engine: '1.5 л атмосферный',
      power: '116 л.с.',
      fuelConsumption: '7.5 л/100км',
      transmission: '5MT механика',
      drive: 'Передний',
      acceleration: '12.8 сек (0-100 км/ч)',
      maxSpeed: '165 км/ч'
    },
    loanOptions: {
      minDownPayment: 20,
      maxTerm: 60,
      interestRate: 8.9
    }
  },
  {
    id: 'mg-zs',
    name: 'MG ZS',
    brand: 'MG',
    model: 'ZS',
    type: 'crossover',
    price: 6500000,
    monthlyPayment: 81000,
    image: 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=500&fit=crop&crop=center',
    description: 'MG ZS - британский стиль в доступном кроссовере.',
    specifications: {
      engine: '1.5 л атмосферный',
      power: '120 л.с.',
      fuelConsumption: '7.1 л/100км',
      transmission: '4AT автомат',
      drive: 'Передний',
      acceleration: '11.2 сек (0-100 км/ч)',
      maxSpeed: '170 км/ч'
    },
    loanOptions: {
      minDownPayment: 15,
      maxTerm: 60,
      interestRate: 7.9
    }
  },
  {
    id: 'dfsk-glory-580',
    name: 'DFSK Glory 580',
    brand: 'DFSK',
    model: 'Glory 580',
    type: 'crossover',
    price: 7100000,
    monthlyPayment: 89000,
    image: 'https://images.unsplash.com/photo-1505073692733-1e4e04e9acd8?w=800&h=500&fit=crop&crop=center',
    description: 'DFSK Glory 580 - просторный 7-местный кроссовер для больших семей.',
    specifications: {
      engine: '1.8 л атмосферный',
      power: '139 л.с.',
      fuelConsumption: '8.2 л/100км',
      transmission: 'CVT вариатор',
      drive: 'Передний',
      acceleration: '11.8 сек (0-100 км/ч)',
      maxSpeed: '175 км/ч'
    },
    loanOptions: {
      minDownPayment: 15,
      maxTerm: 60,
      interestRate: 8.4
    }
  },
  {
    id: 'lifan-x60',
    name: 'Lifan X60',
    brand: 'Lifan',
    model: 'X60',
    type: 'crossover',
    price: 4800000,
    monthlyPayment: 60000,
    image: 'https://images.unsplash.com/photo-1590362891991-f776e747a588?w=800&h=500&fit=crop&crop=center',
    description: 'Lifan X60 - доступный кроссовер с базовым функционалом.',
    specifications: {
      engine: '1.8 л атмосферный',
      power: '128 л.с.',
      fuelConsumption: '8.5 л/100км',
      transmission: '5MT механика',
      drive: 'Передний',
      acceleration: '13.5 сек (0-100 км/ч)',
      maxSpeed: '160 км/ч'
    },
    loanOptions: {
      minDownPayment: 20,
      maxTerm: 60,
      interestRate: 9.4
    }
  },
  {
    id: 'zotye-t600',
    name: 'Zotye T600',
    brand: 'Zotye',
    model: 'T600',
    type: 'crossover',
    price: 5200000,
    monthlyPayment: 65000,
    image: 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=500&fit=crop&crop=center',
    description: 'Zotye T600 - стильный кроссовер с хорошим дизайном.',
    specifications: {
      engine: '1.5 л турбо',
      power: '143 л.с.',
      fuelConsumption: '7.8 л/100км',
      transmission: 'CVT вариатор',
      drive: 'Передний',
      acceleration: '10.8 сек (0-100 км/ч)',
      maxSpeed: '175 км/ч'
    },
    loanOptions: {
      minDownPayment: 20,
      maxTerm: 60,
      interestRate: 9.9
    }
  },
  {
    id: 'wey-vv7',
    name: 'WEY VV7',
    brand: 'WEY',
    model: 'VV7',
    type: 'crossover',
    price: 10500000,
    monthlyPayment: 131000,
    image: 'https://images.unsplash.com/photo-1601362840469-51e4d8d58785?w=800&h=500&fit=crop&crop=center',
    description: 'WEY VV7 - премиальный кроссовер с роскошным интерьером.',
    specifications: {
      engine: '2.0 л турбо',
      power: '227 л.с.',
      fuelConsumption: '8.5 л/100км',
      transmission: '7DCT робот',
      drive: 'Полный AWD',
      acceleration: '8.8 сек (0-100 км/ч)',
      maxSpeed: '205 км/ч'
    },
    loanOptions: {
      minDownPayment: 10,
      maxTerm: 84,
      interestRate: 6.9
    }
  },
  {
    id: 'nio-es6',
    name: 'NIO ES6',
    brand: 'NIO',
    model: 'ES6',
    type: 'electric',
    price: 18500000,
    monthlyPayment: 231000,
    image: 'https://images.unsplash.com/photo-1617886322207-baac93ab248c?w=800&h=500&fit=crop&crop=center',
    description: 'NIO ES6 - премиальный электрический кроссовер с инновационными решениями.',
    specifications: {
      engine: 'Электродвигатель',
      power: '544 л.с.',
      fuelConsumption: '0 л/100км',
      transmission: 'Автоматическая',
      drive: 'Полный AWD',
      acceleration: '4.7 сек (0-100 км/ч)',
      maxSpeed: '200 км/ч',
      batteryCapacity: '100 кВт⋅ч',
      electricRange: '610 км'
    },
    loanOptions: {
      minDownPayment: 10,
      maxTerm: 84,
      interestRate: 5.9
    }
  },
  {
    id: 'xpeng-p7',
    name: 'Xpeng P7',
    brand: 'Xpeng',
    model: 'P7',
    type: 'electric',
    price: 16200000,
    monthlyPayment: 202000,
    image: 'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?w=800&h=500&fit=crop&crop=center',
    description: 'Xpeng P7 - элегантный электрический седан с автопилотом.',
    specifications: {
      engine: 'Электродвигатель',
      power: '316 л.с.',
      fuelConsumption: '0 л/100км',
      transmission: 'Автоматическая',
      drive: 'Задний',
      acceleration: '6.7 сек (0-100 км/ч)',
      maxSpeed: '170 км/ч',
      batteryCapacity: '80.9 кВт⋅ч',
      electricRange: '670 км'
    },
    loanOptions: {
      minDownPayment: 10,
      maxTerm: 84,
      interestRate: 5.9
    }
  },
  {
    id: 'li-auto-one',
    name: 'Li Auto One',
    brand: 'Li Auto',
    model: 'One',
    type: 'hybrid',
    price: 19800000,
    monthlyPayment: 247000,
    image: 'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?w=800&h=500&fit=crop&crop=center',
    description: 'Li Auto One - гибридный кроссовер с увеличенным запасом хода.',
    specifications: {
      engine: '1.2 л турбо + электро',
      power: '326 л.с.',
      fuelConsumption: '2.4 л/100км',
      transmission: 'Автоматическая',
      drive: 'Полный AWD',
      acceleration: '6.5 сек (0-100 км/ч)',
      maxSpeed: '180 км/ч',
      batteryCapacity: '40.5 кВт⋅ч',
      electricRange: '180 км'
    },
    loanOptions: {
      minDownPayment: 10,
      maxTerm: 84,
      interestRate: 6.4
    }
  },
  {
    id: 'chery-omoda-5',
    name: 'Chery Omoda 5',
    brand: 'Chery',
    model: 'Omoda 5',
    type: 'crossover',
    price: 8200000,
    monthlyPayment: 102000,
    image: 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=500&fit=crop&crop=center',
    description: 'Chery Omoda 5 - молодежный кроссовер с современным дизайном.',
    specifications: {
      engine: '1.6 л турбо',
      power: '197 л.с.',
      fuelConsumption: '7.2 л/100км',
      transmission: '7DCT робот',
      drive: 'Передний',
      acceleration: '8.8 сек (0-100 км/ч)',
      maxSpeed: '190 км/ч'
    },
    loanOptions: {
      minDownPayment: 10,
      maxTerm: 84,
      interestRate: 6.4
    }
  },
  {
    id: 'geely-atlas-pro',
    name: 'Geely Atlas Pro',
    brand: 'Geely',
    model: 'Atlas Pro',
    type: 'crossover',
    price: 9800000,
    monthlyPayment: 122000,
    image: 'https://images.unsplash.com/photo-1601362840469-51e4d8d58785?w=800&h=500&fit=crop&crop=center',
    description: 'Geely Atlas Pro - флагманский кроссовер с премиальным оснащением.',
    specifications: {
      engine: '2.0 л турбо',
      power: '190 л.с.',
      fuelConsumption: '8.1 л/100км',
      transmission: '8AT автомат',
      drive: 'Полный AWD',
      acceleration: '9.0 сек (0-100 км/ч)',
      maxSpeed: '195 км/ч'
    },
    loanOptions: {
      minDownPayment: 10,
      maxTerm: 84,
      interestRate: 6.4
    }
  },
  {
    id: 'haval-h6',
    name: 'Haval H6',
    brand: 'Haval',
    model: 'H6',
    type: 'crossover',
    price: 7800000,
    monthlyPayment: 97000,
    image: 'https://images.unsplash.com/photo-1590362891991-f776e747a588?w=800&h=500&fit=crop&crop=center',
    description: 'Haval H6 - популярный кроссовер с надежной репутацией.',
    specifications: {
      engine: '1.5 л турбо',
      power: '150 л.с.',
      fuelConsumption: '7.4 л/100км',
      transmission: '7DCT робот',
      drive: 'Передний',
      acceleration: '10.2 сек (0-100 км/ч)',
      maxSpeed: '175 км/ч'
    },
    loanOptions: {
      minDownPayment: 15,
      maxTerm: 60,
      interestRate: 7.4
    }
  },
  {
    id: 'byd-han-ev',
    name: 'BYD Han EV',
    brand: 'BYD',
    model: 'Han EV',
    type: 'electric',
    price: 17500000,
    monthlyPayment: 218000,
    image: 'https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?w=800&h=500&fit=crop&crop=center',
    description: 'BYD Han EV - флагманский электрический седан с роскошным интерьером.',
    specifications: {
      engine: 'Электродвигатель',
      power: '516 л.с.',
      fuelConsumption: '0 л/100км',
      transmission: 'Автоматическая',
      drive: 'Полный AWD',
      acceleration: '3.9 сек (0-100 км/ч)',
      maxSpeed: '180 км/ч',
      batteryCapacity: '85.4 кВт⋅ч',
      electricRange: '605 км'
    },
    loanOptions: {
      minDownPayment: 10,
      maxTerm: 84,
      interestRate: 5.9
    }
  },
  {
    id: 'changan-cs75-plus',
    name: 'Changan CS75 Plus',
    brand: 'Changan',
    model: 'CS75 Plus',
    type: 'crossover',
    price: 8500000,
    monthlyPayment: 106000,
    image: 'https://images.unsplash.com/photo-1605559424962-9e4c228ac24e?w=800&h=500&fit=crop&crop=center',
    description: 'Changan CS75 Plus - спортивный кроссовер с агрессивным дизайном.',
    specifications: {
      engine: '1.5 л турбо',
      power: '188 л.с.',
      fuelConsumption: '7.6 л/100км',
      transmission: '8AT автомат',
      drive: 'Передний',
      acceleration: '9.1 сек (0-100 км/ч)',
      maxSpeed: '185 км/ч'
    },
    loanOptions: {
      minDownPayment: 15,
      maxTerm: 60,
      interestRate: 7.9
    }
  },
  {
    id: 'geely-coolray',
    name: 'Geely Coolray',
    brand: 'Geely',
    model: 'Coolray',
    type: 'crossover',
    price: 6200000,
    monthlyPayment: 77000,
    image: 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=500&fit=crop&crop=center',
    description: 'Geely Coolray - компактный спортивный кроссовер.',
    specifications: {
      engine: '1.5 л турбо',
      power: '177 л.с.',
      fuelConsumption: '6.8 л/100км',
      transmission: '7DCT робот',
      drive: 'Передний',
      acceleration: '7.9 сек (0-100 км/ч)',
      maxSpeed: '185 км/ч'
    },
    loanOptions: {
      minDownPayment: 15,
      maxTerm: 60,
      interestRate: 7.4
    }
  },
  {
    id: 'haval-f7',
    name: 'Haval F7',
    brand: 'Haval',
    model: 'F7',
    type: 'crossover',
    price: 8900000,
    monthlyPayment: 111000,
    image: 'https://images.unsplash.com/photo-1594736797933-d0401ba2fe65?w=800&h=500&fit=crop&crop=center',
    description: 'Haval F7 - динамичный кроссовер для активного образа жизни.',
    specifications: {
      engine: '2.0 л турбо',
      power: '190 л.с.',
      fuelConsumption: '8.2 л/100км',
      transmission: '7DCT робот',
      drive: 'Передний',
      acceleration: '8.8 сек (0-100 км/ч)',
      maxSpeed: '190 км/ч'
    },
    loanOptions: {
      minDownPayment: 10,
      maxTerm: 84,
      interestRate: 6.9
    }
  },
  {
    id: 'dongfeng-ax7',
    name: 'Dongfeng AX7',
    brand: 'Dongfeng',
    model: 'AX7',
    type: 'crossover',
    price: 7300000,
    monthlyPayment: 91000,
    image: 'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?w=800&h=500&fit=crop&crop=center',
    description: 'Dongfeng AX7 - просторный семейный кроссовер.',
    specifications: {
      engine: '1.6 л турбо',
      power: '166 л.с.',
      fuelConsumption: '7.5 л/100км',
      transmission: '6AT автомат',
      drive: 'Передний',
      acceleration: '10.5 сек (0-100 км/ч)',
      maxSpeed: '180 км/ч'
    },
    loanOptions: {
      minDownPayment: 15,
      maxTerm: 60,
      interestRate: 8.4
    }
  },
  {
    id: 'chery-arrizo-8',
    name: 'Chery Arrizo 8',
    brand: 'Chery',
    model: 'Arrizo 8',
    type: 'sedan',
    price: 6900000,
    monthlyPayment: 86000,
    image: 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=500&fit=crop&crop=center',
    description: 'Chery Arrizo 8 - современный седан с элегантным дизайном.',
    specifications: {
      engine: '1.6 л турбо',
      power: '197 л.с.',
      fuelConsumption: '6.9 л/100км',
      transmission: '7DCT робот',
      drive: 'Передний',
      acceleration: '8.7 сек (0-100 км/ч)',
      maxSpeed: '190 км/ч'
    },
    loanOptions: {
      minDownPayment: 15,
      maxTerm: 60,
      interestRate: 7.9
    }
  },
  {
    id: 'geely-emgrand',
    name: 'Geely Emgrand',
    brand: 'Geely',
    model: 'Emgrand',
    type: 'sedan',
    price: 5400000,
    monthlyPayment: 67000,
    image: 'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&h=500&fit=crop&crop=center',
    description: 'Geely Emgrand - доступный седан с хорошим оснащением.',
    specifications: {
      engine: '1.5 л атмосферный',
      power: '109 л.с.',
      fuelConsumption: '6.2 л/100км',
      transmission: 'CVT вариатор',
      drive: 'Передний',
      acceleration: '11.8 сек (0-100 км/ч)',
      maxSpeed: '170 км/ч'
    },
    loanOptions: {
      minDownPayment: 20,
      maxTerm: 60,
      interestRate: 8.9
    }
  },
  {
    id: 'byd-qin-plus-ev',
    name: 'BYD Qin Plus EV',
    brand: 'BYD',
    model: 'Qin Plus EV',
    type: 'electric',
    price: 10800000,
    monthlyPayment: 135000,
    image: 'https://images.unsplash.com/photo-1617886322207-baac93ab248c?w=800&h=500&fit=crop&crop=center',
    description: 'BYD Qin Plus EV - доступный электрический седан для ежедневных поездок.',
    specifications: {
      engine: 'Электродвигатель',
      power: '184 л.с.',
      fuelConsumption: '0 л/100км',
      transmission: 'Автоматическая',
      drive: 'Передний',
      acceleration: '7.3 сек (0-100 км/ч)',
      maxSpeed: '150 км/ч',
      batteryCapacity: '57.0 кВт⋅ч',
      electricRange: '420 км'
    },
    loanOptions: {
      minDownPayment: 0,
      maxTerm: 84,
      interestRate: 5.9
    }
  }
];

// Helper functions
function getCarById(id) {
  return cars.find(car => car.id === id);
}

function getCarsByType(type) {
  if (type === 'all') return cars;
  return cars.filter(car => car.type === type);
}

function formatPrice(price) {
  return new Intl.NumberFormat('ru-RU').format(price);
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