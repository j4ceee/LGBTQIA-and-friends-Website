const canvas = document.getElementById("canvas");
const ctx = canvas.getContext("2d");

let circleLimit; // max number of circles
let popRadius; // radius of the circle to pop
let circleRadiusMult; // circle radius multiplier (should not be bigger than 50)
let circleSpeedDiv; // circle speed divisor, bigger is slower
let circleRadiusMin;
let circleRadiusMax;

let lightRadius;

updateScreenVars();

function updateScreenVars() {
    if (window.innerWidth < 768 || window.innerHeight < 550) { // mobile
        circleLimit = 8;
        popRadius = 5;
        circleSpeedDiv = 400;
        circleRadiusMin = 10;
        circleRadiusMult = 10;
        circleRadiusMax = circleRadiusMult + circleRadiusMin;

        lightRadius = 500;
    } else { // desktop
        circleLimit = 14;
        popRadius = 10;
        circleSpeedDiv = 300;
        circleRadiusMin = 10;
        circleRadiusMult = 30;
        circleRadiusMax = circleRadiusMult + circleRadiusMin;

        lightRadius = 900;
    }
}

// constants
const currentSpawnMode = "top";
const light = { x: null, y: null };
const globalDirection = { vx: .2, vy: .8 };
const colors = ["#eb3b42", "#ff9101", "#fecb1c", "#60c774", "#02aeda", "#5d5ccc"];

function updateLightPosition() {
    const canvasLightElement = document.getElementById('canvas_light');
    const rect = canvasLightElement.getBoundingClientRect();
    light.x = rect.left + rect.width / 1.55;
    light.y = rect.top + rect.height / 1.6 + window.scrollY;
}

function resize() {
    updateScreenVars();
    const box = canvas.getBoundingClientRect();
    canvas.width = box.width;
    canvas.height = box.height;
    updateLightPosition();
}

function drawLight() {
    // draw the lit area
    ctx.beginPath();
    ctx.arc(light.x, light.y, 10000, 0, 2 * Math.PI);
    let gradient = ctx.createRadialGradient(light.x, light.y, 0, light.x, light.y, lightRadius);
    gradient.addColorStop(0, "#1f4165");
    gradient.addColorStop(1, "#122a4c");
    ctx.fillStyle = gradient;
    ctx.fill();

    // draw the light source (white circle)
    /*
    ctx.beginPath();
    ctx.arc(light.x, light.y, 20, 0, 2 * Math.PI);
    gradient = ctx.createRadialGradient(light.x, light.y, 0, light.x, light.y, 5);
    gradient.addColorStop(0, "#fff");
    gradient.addColorStop(1, "#3b4654");
    ctx.fillStyle = gradient;
    ctx.fill();
     */
}

// Circle class
function Circle(spawn_mode = "def") {
    // colour transition
    this.colorIndex = Math.floor(Math.random() * colors.length); // random starting color
    this.frame = 0; // Current frame
    this.colorTransition = 0;

    // movement
    this.directionOverride = null; // Direction override
    this.directionOverrideFrames = 0; // Number of frames the direction override has been active
    this.vx = (Math.random() - 0.5) / 2; // horizontal velocity
    this.vy = (Math.random() - 0.5) / 2; // vertical velocity

    // circle properties
    this.radius = Math.floor((Math.random() * circleRadiusMult) + circleRadiusMin);
    this.shadow_length = 2000;

    // spawn position
    if (spawn_mode === "def") {
        this.x = Math.floor((Math.random() * canvas.width) + 1);
        this.y = Math.floor((Math.random() * canvas.height) + 1);
    } else if (spawn_mode === "top") {
        this.x = Math.floor((Math.random() * canvas.width) + 1);
        // randomize how far off the screen the circle gets spawned (random between max circle diameter and max circle diameter + 100)
        this.y = -Math.floor((Math.random() * 50) + 2 * circleRadiusMax);
    } else if (spawn_mode === "left") {
        this.x = -Math.floor((Math.random() * 50) + 2 * circleRadiusMax);
        this.y = Math.floor((Math.random() * 0.5 * canvas.height) + 1); // randomize the y position between 0 and half the canvas height from the top
    }

    this.rotate = function() {
        const speed = (60 - this.radius) / circleSpeedDiv;
        let direction;
        if (this.directionOverride) {
            // calculate the lerp factor (0 to 1) based on the remaining directionOverrideFrames
            const lerpFactor = this.directionOverrideFrames / 100; // Assuming 100 is the initial value of directionOverrideFrames

            // linearly interpolate between directionOverride and globalDirection
            direction = {
                vx: this.directionOverride.vx * lerpFactor + globalDirection.vx * (1 - lerpFactor),
                vy: this.directionOverride.vy * lerpFactor + globalDirection.vy * (1 - lerpFactor)
            };
        } else {
            direction = globalDirection;
        }

        this.x += direction.vx * speed;
        this.y += direction.vy * speed;

        // if a direction override is active, decrease its duration
        if (this.directionOverride) {
            this.directionOverrideFrames--;
            if (this.directionOverrideFrames <= 0) {
                // if the direction override has expired, remove it
                this.directionOverride = null;
            }
        }

        // if the circle is off the canvas, remove it and create a new one at the specified spawn mode
        if (this.y - this.radius > canvas.height) { // if the circle is below the canvas
            // delete the circle and create a new one
            circles.splice(circles.indexOf(this), 1);

            // fill array with as many circles as the circleLimit
            while (circles.length < circleLimit) {
                circles.push(new Circle(currentSpawnMode));
            }
        }
        if (this.x - this.radius > canvas.width) { // if the circle is to the right of the canvas
            circles.splice(circles.indexOf(this), 1);
            while (circles.length < circleLimit) {
                circles.push(new Circle("left"));
            }
        }

        // color transition
        this.frame++;
        if (this.frame % 200 === 0) { // Change color every 60 frames
            this.colorIndex = (this.colorIndex + 1) % colors.length;
            this.colorTransition = 0; // Reset the color transition
        } else {
            this.colorTransition += 1 / 200; // Increment the color transition
        }
    }

    // circle drawing
    let circleStroke = 2;

    this.draw = function() {
        if (this.radius > 0) {
            const originalLineWidth = ctx.lineWidth; // Save the original lineWidth

            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius, 0, 2 * Math.PI);
            const color1 = hexToRgb(colors[this.colorIndex]); // Convert the color to RGB
            const color2 = hexToRgb(colors[(this.colorIndex + 1) % colors.length]); // Convert the next color to RGB
            ctx.fillStyle = lerpColor(color1, color2, this.colorTransition); // Use the lerpColor function
            ctx.fill();
            ctx.strokeStyle = "#000000"; // set the color of the stroke
            ctx.lineWidth = circleStroke; // set the width of the stroke
            ctx.stroke(); // apply the stroke

            ctx.lineWidth = originalLineWidth; // restore the original lineWidth
        }
    }

    this.drawShadow = function() {
        const angle = Math.atan2(light.y - this.y, light.x - this.x);
        const endX = this.x + this.shadow_length * Math.sin(-angle - Math.PI / 2);
        const endY = this.y + this.shadow_length * Math.cos(-angle - Math.PI / 2);

        ctx.beginPath();
        ctx.moveTo(this.x, this.y);
        ctx.lineTo(endX, endY);
        ctx.lineWidth = this.radius * 2 + circleStroke;
        ctx.strokeStyle = "#122a4c";
        ctx.stroke();
    }

    // pop function (spawns particles)
    this.pop = function() {
        for (let i = 0; i < 10; i++) {
            particles.push(new Particle(this.x, this.y));
        }
    }
}

// Particle class
function Particle(x, y) {
    this.x = x;
    this.y = y;
    this.vx = Math.random() * 2 - 1;
    this.vy = Math.random() * 2 - 1;
    this.radius = Math.random() * 5 + 2;
    this.lifespan = 100;

    this.update = function() {
        this.x += this.vx;
        this.y += this.vy;
        this.radius -= 0.1; // Decrease the radius
        this.lifespan--;
    }

    this.draw = function() {
        if (this.radius > 0) {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius, 0, 2 * Math.PI);
            ctx.fillStyle = "#fff";
            ctx.fill();
        }
    }
}

const circles = []; // circles array
const particles = []; // pop particles array

function draw() {
    let i;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawLight();

    circles.forEach(item => {
        item.rotate();
        item.drawShadow();
    });
    for (i = 0; i < circles.length; i++) {
        collisionDetection(i);
        if (circles[i]) circles[i].draw();
    }

    // draw particles
    particles.forEach((particle, index) => {
        particle.update();
        particle.draw();
        if (particle.lifespan <= 0) {
            particles.splice(index, 1);
        }
    });

    requestAnimationFrame(draw);
}

resize();
draw();

while (circles.length < circleLimit) {
    circles.push(new Circle());
}

window.onresize = resize;

window.addEventListener('scroll', function() {
    updateLightPosition();
});

function collisionDetection(b) {
    for (let i = circles.length - 1; i >= 0; i--) {
        if (circles[i] && circles[b] && i !== b) {
            // get the distance between the circles
            const dx = (circles[b].x) - (circles[i].x);
            const dy = (circles[b].y) - (circles[i].y);
            const distance = Math.sqrt(dx * dx + dy * dy);
            if (distance < circles[b].radius + circles[i].radius) { // distance < sum of the radius -> collision
                // reduce the radius of the circles by 1 (if the radius is greater than 1)
                circles[b].radius = circles[b].radius > 1 ? circles[b].radius -= 1 : 1;
                circles[i].radius = circles[i].radius > 1 ? circles[i].radius -= 1 : 1;

                // pop the circle if it's radius is less than the popRadius
                if (circles[i] && circles[i].radius < popRadius) {
                    circles[i].pop();
                    // remove the circle from the array
                    circles.splice(i, 1);
                    // add a new circle to the array
                    circles.push(new Circle(currentSpawnMode));
                }
                if (circles[b] && circles[b].radius < popRadius) {
                    circles[b].pop();
                    circles.splice(b, 1);
                    circles.push(new Circle(currentSpawnMode));
                }

                // bounce the circles off each other
                // calculate the velocity of the circles
                let velocityX = dx / distance;
                let velocityY = dy / distance;

                // adjust velocities for each circle
                if (circles[b]) {
                    circles[b].vx = velocityX;
                    circles[b].vy = velocityY;

                    circles[b].directionOverride = { vx: velocityX, vy: velocityY };
                    circles[b].directionOverrideFrames = 100; // Adjust this value to control how long the direction override lasts
                }
                if (circles[i]) {
                    circles[i].vx = -velocityX;
                    circles[i].vy = -velocityY;

                    circles[i].directionOverride = { vx: -velocityX, vy: -velocityY };
                    circles[i].directionOverrideFrames = 100; // Adjust this value to control how long the direction override lasts
                }
            }
        }
    }
}

function hexToRgb(hex) { // convert hex color to RGB
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex); // this regex is used to extract the 3 pairs of hex values from the hex color
    return result ? {
        // for each pair, convert it to an integer using the base 16
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

function lerpColor(a, b, amount) { // transition between two colors
    const ar = a.r;
    const ag = a.g;
    const ab = a.b;
    const br = b.r;
    const bg = b.g;
    const bb = b.b;

    const col_r = ar + amount * (br - ar);
    const col_g = ag + amount * (bg - ag);
    const col_b = ab + amount * (bb - ab);

    return `rgb(${col_r}, ${col_g}, ${col_b})`;
}