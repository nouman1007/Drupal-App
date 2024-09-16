import './side-by-side.scss';
import '../video/video.scss';
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
    media_layout: {
      name: 'Layout',
      description: 'Controls image left/right placement.',
      control: 'select',
      options: {
        'Pin image left': 'left',
        'Pin image right': 'right'
      }
    },
    layout_modifier: {
      name: 'Modifier',
      description: 'Additional classes for the component.',
      control: 'text'
    },
    media_type: {
      name: 'Media Type',
      description: 'Additional classes for the media component.',
      control: 'text'
    },
    body: {
      name: 'Body Text',
      description: 'Side-by-Side body text',
      control: 'text'
    },
    link: {
      url: '#',
      text: 'Find Out More'
    }
  }
};

export const Left_50_50 = SideBySideTemplate.bind({});
Left_50_50.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  media:
    "<img src='./images/card.webp' class='img-fluid rounded' alt='test image' />",
  body: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  media_layout: 'left',
  layout_modifier: '50-50',
  media_type: 'media--type-image',
  link: {
    url: '#',
    text: 'Find Out More'
  }
};

export const Right_50_50 = SideBySideTemplate.bind({});
Right_50_50.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  media:
    "<img src='./images/card.webp' class='img-fluid rounded' alt='test image' />",
  body: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  media_layout: 'right',
  layout_modifier: '50-50',
  media_type: 'media--type-image',
  link: {
    url: '#',
    text: 'Find Out More'
  }
};

export const Left_40_60 = SideBySideTemplate.bind({});
Left_40_60.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  media:
    "<img src='./images/card.webp' class='img-fluid rounded' alt='test image' />",
  body: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  media_layout: 'left',
  layout_modifier: '40-60',
  media_type: 'media--type-image',
  link: {
    url: '#',
    text: 'Find Out More'
  }
};

export const Right_60_40 = SideBySideTemplate.bind({});
Right_60_40.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  media:
    "<img src='./images/card.webp' class='img-fluid rounded' alt='test image' />",
  body: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  media_layout: 'right',
  layout_modifier: '60-40',
  media_type: 'media--type-image',
  link: {
    url: '#',
    text: 'Find Out More'
  }
};

export const Left_30_70 = SideBySideTemplate.bind({});
Left_30_70.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  media:
    "<img src='./images/card.webp' class='img-fluid rounded' alt='test image' />",
  body: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  media_layout: 'left',
  layout_modifier: '30-70',
  media_type: 'media--type-image',
  link: {
    url: '#',
    text: 'Find Out More'
  }
};

export const Right_70_30 = SideBySideTemplate.bind({});
Right_70_30.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  media:
    "<img src='./images/card.webp' class='img-fluid rounded' alt='test image' />",
  body: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  media_layout: 'right',
  layout_modifier: '70-30',
  media_type: 'media--type-image',
  link: {
    url: '#',
    text: 'Find Out More'
  }
};

export const LeftVideo_50_50 = SideBySideTemplate.bind({});
LeftVideo_50_50.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  media:
    "<iframe width='560' height='315' src='https://www.youtube.com/embed/I95hSyocMlg?si=1n8TVSmIkVxSCHxa' title='YouTube video player' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>",
  body: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  media_layout: 'left',
  media_type: 'media--type-video',
  layout_modifier: '50-50',
  link: {
    url: '#',
    text: 'Find Out More'
  }
};

export const RightVideo_50_50 = SideBySideTemplate.bind({});
RightVideo_50_50.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  media:
    "<iframe width='560' height='315' src='https://www.youtube.com/embed/I95hSyocMlg?si=1n8TVSmIkVxSCHxa' title='YouTube video player' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>",
  body: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  media_layout: 'right',
  layout_modifier: '50-50',
  media_type: 'media--type-video',
  link: {
    url: '#',
    text: 'Find Out More'
  }
};

export const LeftVideo_40_60 = SideBySideTemplate.bind({});
LeftVideo_40_60.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  media:
    "<iframe width='560' height='315' src='https://www.youtube.com/embed/I95hSyocMlg?si=1n8TVSmIkVxSCHxa' title='YouTube video player' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>",
  body: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  media_layout: 'left',
  layout_modifier: '40-60',
  media_type: 'media--type-video',
  link: {
    url: '#',
    text: 'Find Out More'
  }
};

export const RightVideo_60_40 = SideBySideTemplate.bind({});
RightVideo_60_40.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  media:
    "<iframe width='560' height='315' src='https://www.youtube.com/embed/I95hSyocMlg?si=1n8TVSmIkVxSCHxa' title='YouTube video player' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>",
  body: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  media_layout: 'right',
  layout_modifier: '60-40',
  media_type: 'media--type-video',
  link: {
    url: '#',
    text: 'Find Out More'
  }
};

export const LeftVideo_30_70 = SideBySideTemplate.bind({});
LeftVideo_30_70.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  media:
    "<iframe width='560' height='315' src='https://www.youtube.com/embed/I95hSyocMlg?si=1n8TVSmIkVxSCHxa' title='YouTube video player' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>",
  body: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  media_layout: 'left',
  layout_modifier: '30-70',
  media_type: 'media--type-video',
  link: {
    url: '#',
    text: 'Find Out More'
  }
};

export const RightVideo_70_30 = SideBySideTemplate.bind({});
RightVideo_70_30.args = {
  eyebrow: 'Eyebrow text',
  title: 'Lorem ipsum dolor sit amet, elit.',
  media:
    "<iframe width='560' height='315' src='https://www.youtube.com/embed/I95hSyocMlg?si=1n8TVSmIkVxSCHxa' title='YouTube video player' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>",
  body: 'Contra legem facit qui id facit quod lex prohibet. Nec dubitamus multa iter quae et nos invenerat. Praeterea iter est quasdam res quas ex communi. Lorem ipsum dolor sit amet, consectetur adipisici elit.',
  media_layout: 'right',
  layout_modifier: '70-30',
  media_type: 'media--type-video',
  link: {
    url: '#',
    text: 'Find Out More'
  }
};
