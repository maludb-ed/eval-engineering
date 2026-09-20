// Shared deck system for the CCAR-P eval-engineering course.
// Palette: instrument teal on slate — measurement, not marketing.
const C = {
  ink: '14262E', inkSoft: '1F3A45', inkLine: '2C4652',
  teal: '1C6B85', tealDeep: '134E61', tealTint: 'E3EEF2', tealPale: 'F1F7F9',
  ochre: 'A9691A', ochreTint: 'F7EDDC',
  gray: '5C6F76', grayLight: '8A9DA3',
  light: 'F4F7F8', white: 'FFFFFF',
};
const HEAD = 'Cambria';
const BODY = 'Calibri';

const M = { x: 0.6, w: 12.13 };           // 13.33" wide canvas
const footerFor = (n, total) => `Module 1 · Success Criteria and Metrics    ${n}/${total}`;

function chip(slide, code, label) {
  slide.addShape('roundRect', {
    x: M.x, y: 0.42, w: 1.02, h: 0.28, rectRadius: 0.05,
    fill: { color: C.teal }, line: { color: C.teal },
  });
  slide.addText(code, {
    x: M.x, y: 0.42, w: 1.02, h: 0.28, isTextBox: true,
    align: 'center', valign: 'middle', margin: 0,
    fontFace: BODY, fontSize: 10, bold: true, color: C.white,
  });
  if (label) {
    slide.addText(label.toUpperCase(), {
      x: M.x + 1.18, y: 0.42, w: 9, h: 0.28, isTextBox: true,
      valign: 'middle', margin: 0, charSpacing: 1.4,
      fontFace: BODY, fontSize: 10, bold: true, color: C.grayLight,
    });
  }
}

function heading(slide, text, opts = {}) {
  slide.addText(text, {
    x: M.x, y: opts.y ?? 0.82, w: opts.w ?? M.w, h: 0.75, isTextBox: true,
    margin: 0, valign: 'top',
    fontFace: HEAD, fontSize: opts.size ?? 32, bold: true, color: opts.color ?? C.ink,
  });
}

function footer(slide, n, total) {
  slide.addText(footerFor(n, total), {
    x: M.x, y: 6.95, w: M.w, h: 0.3, isTextBox: true,
    align: 'right', margin: 0, fontFace: BODY, fontSize: 9, color: C.grayLight,
  });
}

function card(slide, o) {
  slide.addShape('roundRect', {
    x: o.x, y: o.y, w: o.w, h: o.h, rectRadius: 0.06,
    fill: { color: o.fill ?? C.tealPale },
    line: { color: o.line ?? C.tealTint, width: 1 },
  });
}

function statBlock(slide, o) {
  slide.addText(o.value, {
    x: o.x, y: o.y, w: o.w, h: o.vh ?? 0.72, isTextBox: true, margin: 0,
    fontFace: HEAD, fontSize: o.size ?? 40, bold: true, color: o.color ?? C.teal,
  });
  slide.addText(o.label, {
    x: o.x, y: o.y + (o.vh ?? 0.72) - 0.04, w: o.w, h: o.lh ?? 0.75, isTextBox: true, margin: 0,
    fontFace: BODY, fontSize: o.labelSize ?? 12, color: C.gray, lineSpacing: 15,
  });
}

// Numbered or marked row: circle marker + bold lead + description.
function markerRow(slide, o) {
  const d = o.d ?? 0.34;
  slide.addShape('ellipse', {
    x: o.x, y: o.y + 0.04, w: d, h: d,
    fill: { color: o.markFill ?? C.teal }, line: { color: o.markFill ?? C.teal },
  });
  slide.addText(o.mark, {
    x: o.x, y: o.y + 0.04, w: d, h: d, isTextBox: true, margin: 0,
    align: 'center', valign: 'middle',
    fontFace: BODY, fontSize: o.markSize ?? 13, bold: true, color: C.white,
  });
  slide.addText(
    [
      { text: o.lead, options: { bold: true, color: C.ink, breakLine: !!o.body } },
      ...(o.body ? [{ text: o.body, options: { color: C.gray } }] : []),
    ],
    {
      x: o.x + d + 0.22, y: o.y, w: o.w - d - 0.22, h: o.h ?? 0.8, isTextBox: true,
      margin: 0, valign: 'top', fontFace: BODY, fontSize: o.size ?? 14, lineSpacing: 19,
    }
  );
}

module.exports = { C, HEAD, BODY, M, chip, heading, footer, card, statBlock, markerRow };
