document.addEventListener('DOMContentLoaded', () => {
           // Letter Reveal Animation Logic
           const titles = document.querySelectorAll('.ht-title-reveal');
           titles.forEach(title => {
                      const text = title.textContent.trim();
                      if (!text) return;

                      title.innerHTML = '';

                      // Split by words to handle wrapping better, and then by characters within words
                      const words = text.split(/\s+/);

                      let charIndex = 0;
                      words.forEach((word, wordIdx) => {
                                 const wordSpan = document.createElement('span');
                                 wordSpan.style.display = 'inline-block';
                                 wordSpan.style.whiteSpace = 'nowrap';

                                 word.split('').forEach((char) => {
                                            const charSpan = document.createElement('span');
                                            charSpan.textContent = char;
                                            charSpan.className = 'hero-char';
                                            // Adjust delay to suit character count
                                            charSpan.style.transitionDelay = `${(charIndex * 0.05) + 0.1}s`;
                                            wordSpan.appendChild(charSpan);
                                            charIndex++;
                                 });

                                 title.appendChild(wordSpan);

                                 if (wordIdx < words.length - 1) {
                                            const spaceSpan = document.createElement('span');
                                            spaceSpan.innerHTML = '&nbsp;';
                                            title.appendChild(spaceSpan);
                                 }
                      });
           });
});