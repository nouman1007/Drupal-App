import './side-by-side.scss';
import SideBySideTemplate from './side-by-side.twig';

export default {
  title: 'Editorial/Side-by-Side',
  argTypes: {
    eyebrow: {
      name: 'Eyebrow',
      description: 'Side-by-Side eyebrow',
      control: 'text'
    },
      title: {
        name: 'Title',
        description: 'Side-by-Side title',
        control: 'text'
    },
    media: {
      name: 'Media',
      description: 'Side-by-Side image or video markup',
      control: 'text'
    },
    body: {
      name: 'Body Text',
      description: 'Side-by-Side body text',
      control: 'text'
    },
    layout: {
      name: 'Layout',
      description: 'Controls image left/right placement.',
      control: 'select',
      options: {
        'Pin image left': 'left',
        'Pin image right': 'right'
      }
    },
    link: {
      url: '#',
      text: 'Find Out More'
    },
    modifier: {
      name: 'Modifier',
      description: 'Additional classes for the component.',
      control: 'text'
    }
  }
};

export const Left = SideBySideTemplate.bind({});
Left.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  media:
    "<img src='./images/card.webp' class='img-fluid rounded' alt='test image' />",
  body: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  layout: 'left',
  link: {
    url: '#',
    text: 'Find Out More'
  },
  modifier: ''
};

export const Right = SideBySideTemplate.bind({});
Right.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  media:
    "<img src='./images/card.webp' class='img-fluid rounded' alt='test image' />",
  body: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  layout: 'right',
  link: {
    url: '#',
    text: 'Find Out More'
  },
  modifier: ''
};

export const LeftVideo = SideBySideTemplate.bind({});
LeftVideo.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  media:
    "<iframe width='560' height='315' src='https://www.youtube.com/embed/I95hSyocMlg?si=1n8TVSmIkVxSCHxa' title='YouTube video player' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>",
  body: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  layout: 'left',
  link: {
    url: '#',
    text: 'Find Out More'
  },
  modifier: ''
};

export const RightVideo = SideBySideTemplate.bind({});
RightVideo.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  media:
    "<iframe width='560' height='315' src='https://www.youtube.com/embed/I95hSyocMlg?si=1n8TVSmIkVxSCHxa' title='YouTube video player' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>",
  body: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  layout: 'right',
  link: {
    url: '#',
    text: 'Find Out More'
  },
  modifier: ''
};
