import './breaker.scss';
import BreakerTemplate from './breaker.twig';

export default {
  title: 'Editorial/Breaker',
  argTypes: {
    eyebrow: {
      name: 'Eyebrow',
      description: 'Breaker eyebrow',
      control: 'text'
    },
    title: {
      name: 'Title',
      description: 'Breaker title',
      control: 'text'
    },
    summary: {
      name: 'Summary Text',
      description: 'Breaker summary text',
      control: 'text'
    },
    media: {
      name: 'Media',
      description: 'Breaker image or video markup',
      control: 'text'
    },
    media_layout: {
      name: 'Layout',
      description: 'Controls image left/right placement.',
      control: 'select',
      options: {
        'Pin image left': 'left',
        'Pin image right': 'right'
      }
    },
    bg_color_modifier: {
      name: 'Background Color',
      description: 'Background color for the breaker',
      control: 'color',
      options: {
        'Background color white': 'white',
        'Background color grey': 'grey',
        'Background color light-blue': 'blue',
        'Background color purple': 'purple'
      }
    },
    link: {
      url: '#',
      text: 'Find Out More'
    }
  }
};

export const Left_White = BreakerTemplate.bind({});
Left_White.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  summary: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  media:
    "<img src='./images/card.webp' alt='test image' />",
  media_layout: 'left',
  bg_color_modifier: 'white',
  link: {
    url: '#',
    text: 'Find Out More'
  }
};

export const Right_Grey = BreakerTemplate.bind({});
Right_Grey.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  summary: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  media:
    "<img src='./images/card.webp' alt='test image' />",
  media_layout: 'right',
  bg_color_modifier: 'grey',
  link: {
    url: '#',
    text: 'Find Out More'
  }
};

export const Left_Blue = BreakerTemplate.bind({});
Left_Blue.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  summary: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  media:
    "<img src='./images/card.webp' alt='test image' />",
  media_layout: 'left',
  bg_color_modifier: 'blue',
  link: {
    url: '#',
    text: 'Find Out More'
  }
};

export const Right_Purple = BreakerTemplate.bind({});
Right_Purple.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  summary: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  media:
    "<img src='./images/card.webp' alt='test image' />",
  media_layout: 'right',
  bg_color_modifier: 'purple',
  link: {
    url: '#',
    text: 'Find Out More'
  }
};
