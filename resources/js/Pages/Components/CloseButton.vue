<template>
    <button class="button">
        <span class="X"></span>
        <span class="Y"></span>
        <div class="close">Close</div>
    </button>

</template>

<script setup>
</script>

<style scoped>
/* From Uiverse.io by javierBarroso */
.button {
  position: relative;
  width: 100%;
  border: none;
  background: rgba(180, 83, 107, 0.11);
  border-radius: 10px;
  transition: background 0.5s;
  margin: 0px 10px;
}

.X {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  width: 2em;
  height: 1.5px;
  background-color: rgb(255, 255, 255);
  transform: translateX(-50%) rotate(45deg);
}

.Y {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  width: 2em;
  height: 1.5px;
  background-color: #fff;
  transform: translateX(-50%) rotate(-45deg);
}

.close {
  position: absolute;
  display: flex;
  padding: 0.8rem 1.5rem;
  align-items: center;
  justify-content: center;
  transform: translateX(-50%);
  top: -70%;
  left: 50%;
  width: 3em;
  height: 1.7em;
  font-size: 12px;
  background-color: rgb(19, 22, 24);
  color: rgb(187, 229, 236);
  border: none;
  border-radius: 3px;
  pointer-events: none;
  opacity: 0;
}

.button:hover {
  background-color: rgb(211, 21, 21);
}

.button:active {
  background-color: rgb(130, 0, 0);
}

.button:hover > .close {
  animation: close 0.2s forwards 0.25s;
}

@keyframes close {
  100% {
    opacity: 1;
  }
}

</style>
