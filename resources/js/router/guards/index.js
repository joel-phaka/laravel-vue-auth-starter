import beforeEach from "@/router/guards/before-each.js";
import defaultGuards from "@/router/guards/default.guards.js";

export const routeGuards = {
    ...defaultGuards,
}

export default {
    beforeEach,
    routeGuards
}
